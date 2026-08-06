<?php

namespace FOS\ChatBundle\Maker;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Attribute as ODM;
use FOS\ChatBundle\Document\Message;
use FOS\ChatBundle\Document\MessageMetadata;
use FOS\ChatBundle\Document\Thread;
use FOS\ChatBundle\Document\ThreadMetadata;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use function Symfony\Component\String\u;

class Documents extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:fos_chat:documents';
    }

    public static function getCommandDescription(): string
    {
        return 'Generates MongoDB ODM Document classes for FOSChatBundle';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command->addArgument('participantEntFQCN', InputArgument::REQUIRED);
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $participantEntFQCN = $input->getArgument('participantEntFQCN');
        $participantEntClassName = u($participantEntFQCN)->afterLast('\\')->toString();

        $useStatementsBase = [
            ArrayCollection::class,
            [ODM::class => 'ODM'],
            Collection::class,
            ParticipantInterface::class,
            ThreadInterface::class,
            $participantEntFQCN,
        ];

        // 1. Message Document
        $useStatementsMessage = new UseStatementGenerator([
            ...$useStatementsBase,
            [Message::class => 'BaseMessage'],
        ]);

        $messageDocDetails = $generator->createClassNameDetails('FOSChatMessage', 'Document\\FOSChat');
        $generator->generateClass($messageDocDetails->getFullName(), __DIR__ . '/Resources/skeleton/MessageDoc.tpl.php', [
            'use_statements' => $useStatementsMessage,
            'participantEntClassName' => $participantEntClassName,
        ]);

        // 2. Thread Document
        $useStatementsThread = new UseStatementGenerator([
            ...$useStatementsBase,
            [Thread::class => 'BaseThread'],
        ]);

        $threadDocDetails = $generator->createClassNameDetails('FOSChatThread', 'Document\\FOSChat');
        $generator->generateClass($threadDocDetails->getFullName(), __DIR__ . '/Resources/skeleton/ThreadDoc.tpl.php', [
            'use_statements' => $useStatementsThread,
            'participantEntClassName' => $participantEntClassName,
        ]);

        // 3. MessageMetadata Document
        $useStatementsMessageMetadata = new UseStatementGenerator([
            ...$useStatementsBase,
            [MessageMetadata::class => 'BaseMessageMetadata'],
            MessageInterface::class,
        ]);

        $messageMetadataDocDetails = $generator->createClassNameDetails('FOSChatMessageMetadata', 'Document\\FOSChat');
        $generator->generateClass($messageMetadataDocDetails->getFullName(), __DIR__ . '/Resources/skeleton/MessageMetadataDoc.tpl.php', [
            'use_statements' => $useStatementsMessageMetadata,
            'participantEntClassName' => $participantEntClassName,
        ]);

        // 4. ThreadMetadata Document
        $useStatementsThreadMetadata = new UseStatementGenerator([
            ...$useStatementsBase,
            [ThreadMetadata::class => 'BaseThreadMetadata'],
        ]);

        $threadMetadataDocDetails = $generator->createClassNameDetails('FOSChatThreadMetadata', 'Document\\FOSChat');
        $generator->generateClass($threadMetadataDocDetails->getFullName(), __DIR__ . '/Resources/skeleton/ThreadMetadataDoc.tpl.php', [
            'use_statements' => $useStatementsThreadMetadata,
            'participantEntClassName' => $participantEntClassName,
        ]);

        $generator->writeChanges();
    }
}