<?php
namespace FOS\ChatBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Process\Process;


#[AsCommand(name: 'fos_chat:configure', description: 'Finish installation of FOSChatBundle')]
class ConfigureCommand extends Command
{
    private QuestionHelper $qHelper;

    public function __construct
    (
        private string $projectDir,
    )
    {
        parent::__construct();

        $this->qHelper = new QuestionHelper();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['git', 'update-index', '--refresh']);
        $process->run();
        $process = new Process(['git', 'diff-index', '--quiet', 'HEAD', '--']);
        $process->run();

        if (!$process->isSuccessful())
        {
            $output->writeln('<error>You have uncommitted changes. Please commit them first.</error>');
            return Command::FAILURE;
        }

        $generateConfigRes = $this->generateConfig($input, $output);
        if (is_int($generateConfigRes)) {return $generateConfigRes;}

        $getTargetEntsRes = $this->getTargetEnts($input, $output);
        if (is_int($getTargetEntsRes)) {return $getTargetEntsRes;}

        $implementInterfaceRes = $this->implementInterface($output, $getTargetEntsRes['participantEntAbsPath'], 'FOS\ChatBundle\Model\ParticipantInterface as FOSChatParticipantInterface', 'FOSChatParticipantInterface');
        if (is_int($implementInterfaceRes)) {return $implementInterfaceRes;}

        $getTargetEntsRes = $this->generateEntities($output, $getTargetEntsRes, $generateConfigRes['db_driver']);
        if (is_int($getTargetEntsRes)) {return $getTargetEntsRes;}

        $generateMigrationsRes = $this->generateMigrations($output);
        if (is_int($generateMigrationsRes)) {return $generateMigrationsRes;}

        $applyMigrationsRes = $this->applyMigrations($output);
        if (is_int($applyMigrationsRes)) {return $applyMigrationsRes;}

        $output->writeln('<info>✅ Configuration complete!</info>');
        return Command::SUCCESS;
    }

    private function generateConfig(InputInterface $input, OutputInterface $output): int|array
    {
        $db_driver = $this->qHelper->ask($input, $output, new ChoiceQuestion(
            'Please select your DB driver (defaults to orm)',
            ['orm', 'mongo_db'],
            0
        ));
        if ($db_driver === 'mongo_db') 
        {
            $output->writeln('<error>MongoDB is not implemented yet.</error>');
            return Command::FAILURE;
        }

        $yamlAbs = $this->projectDir.'/config/packages/fos_chat.yaml';
        $data = 
        [
            'fos_chat' =>
            [
                'db_driver' =>  $db_driver,
                'thread_class' => 'App\Entity\FOSChat\FOSChatThread',
                'message_class' => 'App\Entity\FOSChat\FOSChatMessage',
            ]
        ];
       
        file_put_contents($yamlAbs, Yaml::dump($data));
        $output->writeln('<info>Created config/packages/fos_chat.yaml</info>');

        return 
        [
            'db_driver' => $db_driver
        ];
    }

    private function getTargetEnts(InputInterface $input, OutputInterface $output): int|array
    {
        $participantEntFQCN = $this->qHelper->ask($input, $output, new Question('Entity which represents the participant (default: App\Entity\User): ', 'App\Entity\User'));

        try 
        {
            $ref = new \ReflectionClass($participantEntFQCN);
        } 
        catch (\ReflectionException $e) 
        {
            $output->writeln("<error>'".$participantEntFQCN."' does not exist</error>");
            return Command::FAILURE;
        }

        return [
            'participantEntFQCN' => $participantEntFQCN,
            'participantEntAbsPath' => $ref->getFileName(),
        ];
    }

    private function generateEntities(OutputInterface $output, array $getTargetEntsRes, string $db_driver): int|bool
    {
        $output->writeln('Generating entities...');
        $process = new Process(['bin/console', 'make:fos_chat:entities', $getTargetEntsRes['participantEntFQCN'], $db_driver]);
        $process->run();
        if (!$process->isSuccessful()) 
        {
            $output->writeln('<error>Failed to generate entities:</error>');
            $output->writeln($process->getErrorOutput());
            return Command::FAILURE;
        }

        $output->writeln('<info>Entities generated!</info>');

        return true;
    }

    private function implementInterface(OutputInterface $output, string $fileAbsPath, string $interfaceUseName, string $interfaceClassName): int|bool
    {
        $code = file_get_contents($fileAbsPath);

        // Add use statement if missing
        if (strpos($code, "use $interfaceUseName;") === false) 
        {
            // insert after namespace declaration
            $code = preg_replace(
                '/^namespace\s+[^;]+;/m',
                "$0\n\nuse $interfaceUseName;",
                $code
            );
        }
        
        // Add interface in class declaration if missing
        $code = preg_replace_callback('/class\s+(\w+)\s*(?:extends\s+(\w+))?\s*(?:implements\s+([^{]+))?/',
            function ($m) use ($interfaceClassName, $output) 
            {
                $className = $m[1];
                $list = !empty($m[3]) ? array_map('trim', explode(',', $m[3])) : [];
                
                if (!in_array($interfaceClassName, $list)) // the interface is not present
                {
                    $list[] = $interfaceClassName;
                    $newImplements = ' implements ' . implode(', ', $list);
                } 
                else // nothing to do
                {
                    return $m[0];
                }

                return 'class ' . $className . (!empty($m[2]) ? ' extends '.$m[2] : '') . $newImplements . PHP_EOL;
            },
            $code
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
