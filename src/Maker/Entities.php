<?php

namespace FOS\ChatBundle\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Entity\Message;
use FOS\ChatBundle\Entity\MessageMetadata;
use FOS\ChatBundle\Entity\Thread;
use FOS\ChatBundle\Entity\ThreadMetadata;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use Symfony\Component\Console\Input\InputArgument;
use function Symfony\Component\String\u;


class Entities extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:fos_chat:entities';
    }

    public static function getCommandDescription(): string
    {
        return '';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->addArgument('participantEntFQCN', InputArgument::REQUIRED)
            ->addArgument('db_driver', InputArgument::REQUIRED)
        ;
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $db_driver = $input->getArgument('db_driver');

        if ($db_driver === 'orm')       {$this->generateOrm($input, $io, $generator);}
        if ($db_driver === 'mongo_db')  {$this->generateMongoDb($input, $io, $generator);}
    }

    private function generateOrm(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $participantEntFQCN = $input->getArgument('participantEntFQCN');
        $participantEntClassName = (u($participantEntFQCN)->afterLast('\\'));

        $useStatementsBase = [
            ArrayCollection::class,
            [Mapping::class => 'ORM'],
            Collection::class,
            [Message::class => 'BaseMessage'],
            ParticipantInterface::class,
            ThreadInterface::class,
            $participantEntFQCN,
        ];

        $useStatementsMessage = new UseStatementGenerator(
            [
                ...$useStatementsBase,
                [Message::class => 'BaseMessage'],
            ]);

        $messageEntClassNameDetails = $generator->createClassNameDetails(
            'FOSChatMessage',
            'Entity\\FOSChat',
        );

        $generator->generateClass(
            $messageEntClassNameDetails->getFullName(),
            __DIR__.'/Resources/skeleton/MessageEnt.tpl.php',
            [
                'use_statements' => $useStatementsMessage,
                'participantEntClassName' => $participantEntClassName]
        );


        $useStatementsThread = new UseStatementGenerator(
            [
                ...$useStatementsBase,
                [Thread::class => 'BaseThread'],
            ]);

        $threadEntClassNameDetails = $generator->createClassNameDetails(
            'FOSChatThread',
            'Entity\\FOSChat',
        );

        $generator->generateClass(
            $threadEntClassNameDetails->getFullName(),
            __DIR__.'/Resources/skeleton/ThreadEnt.tpl.php',
            [
                'use_statements' => $useStatementsThread,
                'participantEntClassName' => $participantEntClassName]
        );


        $useStatementsMessageMetadata = new UseStatementGenerator(
            [
                ...$useStatementsBase,
                [MessageMetadata::class => 'BaseMessageMetadata'],
                MessageInterface::class
            ]);

        $messageMetadataEntClassNameDetails = $generator->createClassNameDetails(
            'FOSChatMessageMetadata',
            'Entity\\FOSChat',
        );

        $generator->generateClass(
            $messageMetadataEntClassNameDetails->getFullName(),
            __DIR__.'/Resources/skeleton/MessageMetadataEnt.tpl.php',
            [
                'use_statements' => $useStatementsMessageMetadata,
                'participantEntClassName' => $participantEntClassName]
        );


        $useStatementsThreadMetadata = new UseStatementGenerator(
            [
                ...$useStatementsBase,
                [ThreadMetadata::class => 'BaseThreadMetadata'],
            ]);

        $threadMetadataEntClassNameDetails = $generator->createClassNameDetails(
            'FOSChatThreadMetadata',
            'Entity\\FOSChat',
        );

        $generator->generateClass(
            $threadMetadataEntClassNameDetails->getFullName(),
            __DIR__.'/Resources/skeleton/ThreadMetadataEnt.tpl.php',
            [
                'use_statements' => $useStatementsThreadMetadata,
                'participantEntClassName' => $participantEntClassName]
        );
        

        $generator->writeChanges();
    }

    private function generateMongoDb(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {

    }
}