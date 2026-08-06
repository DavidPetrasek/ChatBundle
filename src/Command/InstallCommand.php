<?php
namespace FOS\ChatBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

#[AsCommand(name: 'fos_chat:install', description: 'Finish installation of FOSChatBundle')]
class InstallCommand extends Command
{
    private readonly QuestionHelper $qHelper;

    public function __construct
    (
        private readonly string $projectDir,
    )
    {
        parent::__construct();
        $this->qHelper = new QuestionHelper();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['git', 'status', '--porcelain']);
        $process->run();

        if ($process->isSuccessful() && !empty(trim($process->getOutput()))) 
        {
            $output->writeln('<error>You have uncommitted changes.</error>');
            $continue = $this->qHelper->ask($input, $output, new ConfirmationQuestion('Do you want to continue anyway? [y/N] ', false));
            if (!$continue) return Command::FAILURE;
        }

        $generateConfigRes = $this->generateConfig($input, $output);
        if (is_int($generateConfigRes)) return $generateConfigRes;

        $getTargetEntsRes = $this->getTargetEnts($input, $output, $generateConfigRes['db_driver']);
        if (is_int($getTargetEntsRes)) return $getTargetEntsRes;

        $implementInterfaceRes = $this->implementInterface
        (
            $output,
            $getTargetEntsRes['participantEntAbsPath'],
            'FOS\ChatBundle\Model\ParticipantInterface as FOSChatParticipantInterface',
            'FOSChatParticipantInterface'
        );
        if (is_int($implementInterfaceRes)) return $implementInterfaceRes;

        $generateClassesRes = $this->generateClasses($output, $getTargetEntsRes, $generateConfigRes['db_driver']);
        if (is_int($generateClassesRes)) return $generateClassesRes;

        // Run SQL migrations ONLY for ORM driver
        if ($generateConfigRes['db_driver'] === 'orm') 
        {
            $generateMigrationsRes = $this->generateMigrations($output);
            if (is_int($generateMigrationsRes)) return $generateMigrationsRes;

            $applyMigrationsRes = $this->applyMigrations($output);
            if (is_int($applyMigrationsRes)) return $applyMigrationsRes;
        }

        $output->writeln('<info>✅ Installation complete!</info>');
        return Command::SUCCESS;
    }

    private function generateConfig(InputInterface $input, OutputInterface $output): array
    {
        $db_driver = $this->qHelper->ask($input, $output, new ChoiceQuestion(
            'Please select your DB driver (defaults to orm)',
            ['orm', 'mongodb'],
            0
        ));
        
        $fqcnType = $db_driver === 'orm' ? 'Entity' : 'Document';
        $yamlAbs = $this->projectDir.'/config/packages/fos_chat.yaml';
        $data = 
        [
            'fos_chat' => 
            [
                'db_driver' => $db_driver,
                'thread_class' => "App\\{$fqcnType}\\FOSChat\\FOSChatThread",
                'message_class' => "App\\{$fqcnType}\\FOSChat\\FOSChatMessage",
            ],
        ];

        file_put_contents($yamlAbs, Yaml::dump($data, 4));
        $output->writeln('<info>Created config/packages/fos_chat.yaml</info>');

        return 
        [
            'db_driver' => $db_driver
        ];
    }

    private function getTargetEnts(InputInterface $input, OutputInterface $output, string $db_driver): int|array
    {
        $defaultFqcn = $db_driver === 'orm' ? 'Entity' : 'Document';
        $participantEntFQCN = $this->qHelper->ask($input, $output,
            new Question("Class representing the participant (default: App\\{$defaultFqcn}\\User): ", "App\\{$defaultFqcn}\\User")
        );

        try 
        {
            $ref = new \ReflectionClass($participantEntFQCN);
        } 
        catch (\ReflectionException) 
        {
            $output->writeln("<error>'{$participantEntFQCN}' class does not exist.</error>");
            return Command::FAILURE;
        }

        return 
        [
            'participantEntFQCN' => $participantEntFQCN,
            'participantEntAbsPath' => $ref->getFileName(),
        ];
    }

    private function generateClasses(OutputInterface $output, array $getTargetEntsRes, string $db_driver): int|bool
    {
        $cmdName = $db_driver === 'orm' ? 'entities' : 'documents';

        $output->writeln('Generating classes...');
        $process = new Process(['bin/console', 'make:fos_chat:'.$cmdName, $getTargetEntsRes['participantEntFQCN']]);
        $process->run();
        if (!$process->isSuccessful()) 
        {
            $output->writeln('<error>Failed to generate classes:</error>');
            $output->writeln($process->getErrorOutput());
            return Command::FAILURE;
        }

        $output->writeln('<info>Classes generated!</info>');
        return true;
    }

    private function implementInterface(OutputInterface $output, string $fileAbsPath, string $interfaceUseName, string $interfaceClassName): bool
    {
        $code = file_get_contents($fileAbsPath);

        // Add use statement if missing
        if (!str_contains($code, "use {$interfaceUseName};")) 
        {
            $code = preg_replace('/^namespace\s+[^;]+;/m', "$0\n\nuse {$interfaceUseName};", $code, 1);
        }

        // Add interface implementation
        $code = preg_replace_callback
        (
            '/(class\s+\w+)(?:\s+extends\s+[\w\\\\_]+)?(?:\s+implements\s+([^{]+))?/i',
            function ($m) use ($interfaceClassName): string 
            {
                $implementsList = !empty($m[2]) ? array_map('trim', explode(',', $m[2])) : [];

                if (!in_array($interfaceClassName, $implementsList, true)) 
                {
                    $implementsList[] = $interfaceClassName;
                    return $m[1] . (str_contains($m[0], 'extends') ? preg_replace('/.*(extends\s+[\w\\\\_]+).*/i', ' $1', $m[0]) : '') . ' implements ' . implode(', ', $implementsList);
                }

                return $m[0];
            },
            $code,
            1
        );

        file_put_contents($fileAbsPath, $code);
        $output->writeln('<info>Interface added to '. str_replace($this->projectDir.'/', '', $fileAbsPath) .'</info>');

        return true;
    }

    private function generateMigrations(OutputInterface $output): bool|int
    {
        $output->writeln('Generating migration...');
        $makeMigrationProcess = new Process(['bin/console', 'make:migration']);
        $makeMigrationProcess->run();

        if (!$makeMigrationProcess->isSuccessful()) 
        {
            $output->writeln('<error>Failed to generate migration:</error>');
            $output->writeln($makeMigrationProcess->getErrorOutput());
            return Command::FAILURE;
        }

        $output->writeln('<info>Migration generated!</info>');

        return true;
    }

    private function applyMigrations(OutputInterface $output): bool|int
    {
        $output->writeln('Applying migrations...');
        $applyMigrationProcess = new Process(['bin/console', 'doctrine:migrations:migrate', '--no-interaction']);
        $applyMigrationProcess->run();
        if (!$applyMigrationProcess->isSuccessful()) 
        {
            $output->writeln('<error>Failed to apply migration:</error>');
            $output->writeln($applyMigrationProcess->getErrorOutput());
            return Command::FAILURE;
        }

        $output->writeln('<info>Migrations applied!</info>');

        return true;
    }
}