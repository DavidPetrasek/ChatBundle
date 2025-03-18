<?php

namespace FOS\ChatBundle\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\QueryBuilder;
use FOS\ChatBundle\Document\Thread;
use FOS\ChatBundle\Document\ThreadMetadata;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\Model\ThreadMetadata as ModelThreadMetadata;
use FOS\ChatBundle\ModelManager\ThreadManager as BaseThreadManager;

/**
 * Default MongoDB ThreadManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ThreadManager extends BaseThreadManager
{
    private $repository;

    private string $class;

    private string $metaClass;

    public function __construct(private DocumentManager $dm, string $class, string $metaClass, private MessageManager $messageManager)
    {
        $this->repository = $dm->getRepository($class);
        $this->class = $dm->getClassMetadata($class)->name;
        $this->metaClass = $dm->getClassMetadata($metaClass)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadById($id) : ?ThreadInterface
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant): QueryBuilder
    {
        return $this->repository->createQueryBuilder()
            ->field('activeRecipients')->equals($participant->getId())
            /* TODO: Sort by date of the last message written by another
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantInboxThreads(ParticipantInterface $participant): array
    {
        return $this->getParticipantInboxThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $participant): QueryBuilder
    {
        return $this->repository->createQueryBuilder()
            ->field('activeSenders')->equals($participant->getId())
            /* TODO: Sort by date of the last message written by this
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantSentThreads(ParticipantInterface $participant): array
    {
        return $this->getParticipantSentThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantDeletedThreadsQueryBuilder(ParticipantInterface $participant): QueryBuilder
    {
        return $this->repository->createQueryBuilder()
            ->field('metadata.isDeleted')->equals(true)
            ->field('metadata.participant.$id')->equals(new \MongoId($participant->getId()))
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantDeletedThreads(ParticipantInterface $participant): array
    {
        return $this->getParticipantDeletedThreadsQueryBuilder($participant)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search): QueryBuilder
    {
        // remove all non-word chars
        $search = preg_replace('/[^\w]/', ' ', trim((string) $search));
        // build a regex like (term1|term2)
        $regex = sprintf('/(%s)/', implode('|', explode(' ', (string) $search)));

        return $this->repository->createQueryBuilder()
            ->field('activeParticipants')->equals($participant->getId())
            // Note: This query is not anchored, so "keywords" need not be indexed
            ->field('keywords')->equals(new \MongoRegex($regex))
            /* TODO: Sort by date of the last message written by this
             * participant, as is done for ORM. This is not possible with the
             * current schema; compromise by sorting by last message date.
             */
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantThreadsBySearch(ParticipantInterface $participant, $search): array
    {
        return $this->getParticipantThreadsBySearchQueryBuilder($participant, $search)->getQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadsCreatedBy(ParticipantInterface $participant): array
    {
        return $this->repository->createQueryBuilder()
            ->field('createdBy.$id')->equals(new \MongoId($participant->getId()))
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markIsReadByThreadAndParticipant($readable, $participant, false);
    }

    /**
     * {@inheritdoc}
     */
    public function saveThread(ThreadInterface $thread, $andFlush = true): void
    {
        $this->denormalize($thread);
        $this->dm->persist($thread);
        if ($andFlush) {
            $this->dm->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteThread(ThreadInterface $thread): void
    {
        $this->dm->remove($thread);
        $this->dm->flush();
    }

    /**
     * Returns the fully qualified comment thread class name.
     */
    public function getClass() : string
    {
        return $this->class;
    }

    /**
     * Creates a new ThreadMetadata instance. 
     */
    private function createThreadMetadata() : ThreadMetadata
    {
        return new $this->metaClass();
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */
    /**
     * Performs denormalization tricks.
     */
    private function denormalize(Thread $thread)
    {
        $this->doParticipants($thread);
        $this->doEnsureThreadMetadataExists($thread);
        $thread->denormalize();

        foreach ($thread->getMessages() as $message) {
            $this->messageManager->denormalize($message);
        }
    }

    /**
     * Ensures that the thread participants are up to date.
     */
    private function doParticipants(Thread $thread)
    {
        foreach ($thread->getMessages() as $message) {
            $thread->addParticipant($message->getSender());
        }
    }

    /**
     * Ensures that metadata exists for each thread participant and that the
     * last message dates are current.
     */
    private function doEnsureThreadMetadataExists(Thread $thread)
    {
        foreach ($thread->getParticipants() as $participant) {
            if (!($meta = $thread->getMetadataForParticipant($participant)) instanceof ModelThreadMetadata) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($participant);
                $thread->addMetadata($meta);
            }
        }
    }
}
