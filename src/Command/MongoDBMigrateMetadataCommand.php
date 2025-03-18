<?php

namespace FOS\ChatBundle\Command;

use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\ChatBundle\Document\Message;
use FOS\ChatBundle\Document\Thread;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MongoDBMigrateMetadataCommand extends ContainerAwareCommand
{
    private ?\MongoCollection $messageCollection = null;

    private ?\MongoCollection $threadCollection = null;

    private ?\MongoCollection $participantCollection = null;

    private ?array $updateOptions = null;

    private ?\Closure $printStatusCallback = null;

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (!$this->getContainer()->has('doctrine.odm.mongodb')) {
            return false;
        }

        return parent::isEnabled();
    }

    /**
     * {@inheritdoc}
     */
    private function configure()
    {
        $this
            ->setName('fos:message:mongodb:migrate:metadata')
            ->setDescription('Migrates document hash fields to embedded metadata and active/unread arrays')
            ->addArgument('participantClass', InputArgument::REQUIRED, 'Participant class')
            ->addOption('safe', null, InputOption::VALUE_OPTIONAL, 'Mongo update option', false)
            ->addOption('fsync', null, InputOption::VALUE_OPTIONAL, 'Mongo update option', false)
            ->setHelp(<<<'EOT'
The <info>fos:message:mongodb:migrate:metadata</info> command migrates old document hash
fields to a new schema optimized for MongoDB queries. This command requires the
participant class to be provided as its first and only parameter:

  <info>php app/console fos:message:mongodb:migrate:metadata "Acme\Document\User"</info>

The following hash fields will become obsolete after migration:

  <info>*</info> message.isReadByParticipant
  <info>*</info> thread.datesOfLastMessageWrittenByOtherParticipant
  <info>*</info> thread.datesOfLastMessageWrittenByParticipant
  <info>*</info> thread.isDeletedByParticipant

The following new fields will be created:

  <info>*</info> message.metadata <comment>(array of embedded metadata documents)</comment>
  <info>*</info> message.unreadForParticipants <comment>(array of participant ID's)</comment>
  <info>*</info> thread.activeParticipants <comment>(array of participant ID's)</comment>
  <info>*</info> thread.activeRecipients <comment>(array of participant ID's)</comment>
  <info>*</info> thread.activeSenders <comment>(array of participant ID's)</comment>
  <info>*</info> thread.lastMessageDate <comment>(timestamp of the most recent message)</comment>
  <info>*</info> thread.metadata <comment>(array of embedded metadata documents)</comment>

<info>Note:</info> This migration script will not unset any obsolete fields, which will
preserve backwards compatibility. You may manually remove those fields from
message and thread documents at your own discretion.
EOT
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    private function initialize(InputInterface $input, OutputInterface $output)
    {
        $registry = $this->getContainer()->get('doctrine.odm.mongodb');

        $this->messageCollection = $this->getMongoCollectionForClass($registry, $this->getContainer()->getParameter('fos_chat.message_class'));
        $this->threadCollection = $this->getMongoCollectionForClass($registry, $this->getContainer()->getParameter('fos_chat.thread_class'));
        $this->participantCollection = $this->getMongoCollectionForClass($registry, $input->getArgument('participantClass'));

        $this->updateOptions = [
            'multiple' => false,
            'safe' => $input->getOption('safe'),
            'fsync' => $input->getOption('fsync'),
        ];

        $this->printStatusCallback = function (): void {
        };
        register_tick_function($this->printStatus(...));
    }

    /**
     * {@inheritdoc}
     */
    private function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrateMessages($output);
        $this->migrateThreads($output);

        $size = memory_get_peak_usage(true);
        $unit = ['b', 'k', 'm', 'g', 't', 'p'];
        $output->writeln(sprintf('Peak Memory Usage: <comment>%s</comment>', round($size / 1024 ** $i = floor(log($size, 1024)), 2).$unit[$i]));
    }

    /**
     * Migrate message documents.
     */
    private function migrateMessages(OutputInterface $output): void
    {
        $cursor = $this->messageCollection->find(
            ['metadata' => ['$exists' => false]],
            [
                'isReadByParticipant' => 1,
                'isSpam' => 1,
            ]
        );
        $cursor->snapshot();

        $numProcessed = 0;

        if (!$numTotal = $cursor->count()) {
            $output->writeln('There are no message documents to migrate.');

            return;
        }

        $this->printStatusCallback = function () use ($output, &$numProcessed, $numTotal): void {
            $output->write(sprintf("Processed: <info>%d</info> / Complete: <info>%d%%</info>\r", $numProcessed, round(100 * ($numProcessed / $numTotal))));
        };

        declare(ticks=2500) {
            foreach ($cursor as $message) {
                $this->createMessageMetadata($message);
                $this->createMessageUnreadForParticipants($message);

                $this->messageCollection->update(
                    ['_id' => $message['_id']],
                    ['$set' => [
                        'metadata' => $message['metadata'],
                        'unreadForParticipants' => $message['unreadForParticipants'],
                    ]],
                    $this->updateOptions
                );
                ++$numProcessed;
            }
        }

        $output->write(str_repeat(' ', 28 + ceil(log10($numProcessed)))."\r");
        $output->writeln(sprintf('Migrated <info>%d</info> message documents.', $numProcessed));
    }

    /**
     * Migrate thread documents.
     */
    private function migrateThreads(OutputInterface $output): void
    {
        $cursor = $this->threadCollection->find(
            ['metadata' => ['$exists' => false]],
            [
                'datesOfLastMessageWrittenByOtherParticipant' => 1,
                'datesOfLastMessageWrittenByParticipant' => 1,
                'isDeletedByParticipant' => 1,
                'isSpam' => 1,
                'messages' => 1,
                'participants' => 1,
            ]
        );

        $numProcessed = 0;

        if (!$numTotal = $cursor->count()) {
            $output->writeln('There are no thread documents to migrate.');

            return;
        }

        $this->printStatusCallback = function () use ($output, &$numProcessed, $numTotal): void {
            $output->write(sprintf("Processed: <info>%d</info> / Complete: <info>%d%%</info>\r", $numProcessed, round(100 * ($numProcessed / $numTotal))));
        };

        declare(ticks=2500) {
            foreach ($cursor as $thread) {
                $this->createThreadMetadata($thread);
                $this->createThreadLastMessageDate($thread);
                $this->createThreadActiveParticipantArrays($thread);

                $this->threadCollection->update(
                    ['_id' => $thread['_id']],
                    ['$set' => [
                        'activeParticipants' => $thread['activeParticipants'],
                        'activeRecipients' => $thread['activeRecipients'],
                        'activeSenders' => $thread['activeSenders'],
                        'lastMessageDate' => $thread['lastMessageDate'],
                        'metadata' => $thread['metadata'],
                    ]],
                    $this->updateOptions
                );
                ++$numProcessed;
            }
        }

        $output->write(str_repeat(' ', 28 + ceil(log10($numProcessed)))."\r");
        $output->writeln(sprintf('Migrated <info>%d</info> thread documents.', $numProcessed));
    }

    /**
     * Sets the metadata array on the message.
     *
     * By default, Mongo will not include "$db" when creating the participant
     * reference. We'll add that manually to be consistent with Doctrine.
     */
    private function createMessageMetadata(array &$message): void
    {
        $metadata = [];

        foreach ($message['isReadByParticipant'] as $participantId => $isRead) {
            $metadata[] = [
                'isRead' => $isRead,
                'participant' => $this->participantCollection->createDBRef(['_id' => new \MongoId($participantId)]) + ['$db' => (string) $this->participantCollection->db],
            ];
        }

        $message['metadata'] = $metadata;
    }

    /**
     * Sets the unreadForParticipants array on the message.
     *
     * @see Message::doEnsureUnreadForParticipantsArray()
     */
    private function createMessageUnreadForParticipants(array &$message): void
    {
        $unreadForParticipants = [];

        if (!$message['isSpam']) {
            foreach ($message['metadata'] as $metadata) {
                if (!$metadata['isRead']) {
                    $unreadForParticipants[] = (string) $metadata['participant']['$id'];
                }
            }
        }

        $message['unreadForParticipants'] = $unreadForParticipants;
    }

    /**
     * Sets the metadata array on the thread.
     *
     * By default, Mongo will not include "$db" when creating the participant
     * reference. We'll add that manually to be consistent with Doctrine.
     */
    private function createThreadMetadata(array &$thread): void
    {
        $metadata = [];

        $participantIds = array_keys($thread['datesOfLastMessageWrittenByOtherParticipant'] + $thread['datesOfLastMessageWrittenByParticipant'] + $thread['isDeletedByParticipant']);

        foreach ($participantIds as $participantId) {
            $meta = [
                'isDeleted' => false,
                'participant' => $this->participantCollection->createDBRef(['_id' => new \MongoId($participantId)]) + ['$db' => (string) $this->participantCollection->db],
            ];

            if (isset($thread['isDeletedByParticipant'][$participantId])) {
                $meta['isDeleted'] = $thread['isDeletedByParticipant'][$participantId];
            }

            if (isset($thread['datesOfLastMessageWrittenByOtherParticipant'][$participantId])) {
                $meta['lastMessageDate'] = new \MongoDate($thread['datesOfLastMessageWrittenByOtherParticipant'][$participantId]);
            }

            if (isset($thread['datesOfLastMessageWrittenByParticipant'][$participantId])) {
                $meta['lastParticipantMessageDate'] = new \MongoDate($thread['datesOfLastMessageWrittenByParticipant'][$participantId]);
            }

            $metadata[] = $meta;
        }

        $thread['metadata'] = $metadata;
    }

    /**
     * Sets the lastMessageDate timestamp on the thread.
     */
    private function createThreadLastMessageDate(array &$thread): void
    {
        $lastMessageRef = end($thread['messages']);

        if (false !== $lastMessageRef) {
            $lastMessage = $this->messageCollection->findOne(
                ['_id' => $lastMessageRef['$id']],
                ['createdAt' => 1]
            );
        }

        $thread['lastMessageDate'] = $lastMessage['createdAt'] ?? null;
    }

    /**
     * Sets the active participant arrays on the thread.
     *
     * @see Thread::doEnsureActiveParticipantArrays()
     */
    private function createThreadActiveParticipantArrays(array &$thread): void
    {
        $activeParticipants = [];
        $activeRecipients = [];
        $activeSenders = [];

        foreach ($thread['participants'] as $participantRef) {
            foreach ($thread['metadata'] as $metadata) {
                if ($metadata['isDeleted'] && $metadata['participant']['$id'] === $participantRef['$id']) {
                    continue 2;
                }
            }

            $participantIsActiveRecipient = false;
            $participantIsActiveSender = false;

            foreach ($thread['messages'] as $messageRef) {
                $message = $this->threadCollection->getDBRef($messageRef);

                if (null === $message) {
                    throw new \UnexpectedValueException(sprintf('Message "%s" not found for thread "%s"', $messageRef['$id'], $thread['_id']));
                }

                if (!isset($message['sender']['$id'])) {
                    throw new \UnexpectedValueException(sprintf('Sender reference not found for message "%s"', $messageRef['$id']));
                }

                if ($message['sender']['$id'] == $participantRef['$id']) {
                    $participantIsActiveSender = true;
                } elseif (!$thread['isSpam']) {
                    $participantIsActiveRecipient = true;
                }

                if ($participantIsActiveRecipient && $participantIsActiveSender) {
                    break;
                }
            }

            if ($participantIsActiveSender) {
                $activeSenders[] = (string) $participantRef['$id'];
            }

            if ($participantIsActiveRecipient) {
                $activeRecipients[] = (string) $participantRef['$id'];
            }

            if ($participantIsActiveSender || $participantIsActiveRecipient) {
                $activeParticipants[] = (string) $participantRef['$id'];
            }
        }

        $thread['activeParticipants'] = $activeParticipants;
        $thread['activeRecipients'] = $activeRecipients;
        $thread['activeSenders'] = $activeSenders;
    }

    /**
     * Get the MongoCollection for the given class.
     * @throws \RuntimeException if the class has no DocumentManager
     */
    private function getMongoCollectionForClass(ManagerRegistry $registry, string $class): \MongoCollection
    {
        if (!$dm = $registry->getManagerForClass($class)) {
            throw new \RuntimeException(sprintf('There is no DocumentManager for class "%s"', $class));
        }

        return $dm->getDocumentCollection($class)->getMongoCollection();
    }

    /**
     * Invokes the print status callback.
     *
     * Since unregister_tick_function() does not support anonymous functions, it
     * is easier to register one method (this) and invoke a dynamic callback.
     */
    public function printStatus(): void
    {
        call_user_func($this->printStatusCallback);
    }
}
