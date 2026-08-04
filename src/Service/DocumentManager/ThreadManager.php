<?php

namespace FOS\ChatBundle\Service\DocumentManager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Query\Builder;
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
    private readonly string $class;
    private readonly string $metaClass;

    public function __construct
    (
        private readonly DocumentManager $dm,
        string $class,
        string $metaClass,
        private readonly MessageManager $messageManager
    ) 
    {
        $this->repository = $dm->getRepository($class);
        $this->class = $dm->getClassMetadata($class)->name;
        $this->metaClass = $dm->getClassMetadata($metaClass)->name;
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadById(int $id): ?ThreadInterface
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantThreadsQueryBuilder(int|ParticipantInterface $participant): Builder
    {
        return $this->repository->createQueryBuilder()
            ->field('metadata.participant')->equals($participant);
    }

    /**
     * {@inheritdoc}
     */
    public function getNbParticipantThreadsQueryBuilder(int|ParticipantInterface $participant): Builder
    {
        return $this->getParticipantThreadsQueryBuilder($participant);
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantInboxThreadsQueryBuilder(int|ParticipantInterface $participant): Builder
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
    public function findParticipantInboxThreads(int|ParticipantInterface $participant): array
    {
        return $this->getParticipantInboxThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantSentThreadsQueryBuilder(int|ParticipantInterface $participant): Builder
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
    public function findParticipantSentThreads(int|ParticipantInterface $participant): array
    {
        return $this->getParticipantSentThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantDeletedThreadsQueryBuilder(int|ParticipantInterface $participant): Builder
    {
        return $this->repository->createQueryBuilder()
            ->field('metadata.deleted')->equals(true)
            ->field('metadata.participant')->equals($participant)
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantDeletedThreads(int|ParticipantInterface $participant): array
    {
        return $this->getParticipantDeletedThreadsQueryBuilder($participant)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getParticipantThreadsBySearchQueryBuilder(int|ParticipantInterface $participant, string $search): Builder
    {
        // remove all non-word chars
        $search = preg_replace('/[^\w]/', ' ', trim($search));
        // build a regex like (term1|term2)
        $regex = sprintf('(%s)', implode('|', array_filter(explode(' ', $search))));

        return $this->repository->createQueryBuilder()
            ->field('activeParticipants')->equals($participant->getId())
            ->field('keywords')->equals(new \MongoDB\BSON\Regex($regex, 'i'))
            ->sort('lastMessageDate', 'desc');
    }

    /**
     * {@inheritdoc}
     */
    public function findParticipantThreadsBySearch(int|ParticipantInterface $participant, string $search): array
    {
        return $this->getParticipantThreadsBySearchQueryBuilder($participant, $search)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function getThreadsCreatedByParticipantQueryBuilder(int|ParticipantInterface $participant): Builder
    {
        return $this->repository->createQueryBuilder()
            ->field('createdBy')->equals($participant);
    }

    /**
     * {@inheritdoc}
     */
    public function findThreadsCreatedBy(int|ParticipantInterface $participant): array
    {
        return $this->getThreadsCreatedByParticipantQueryBuilder($participant)
            ->getQuery()
            ->execute()
            ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markReadByThreadAndParticipant($readable, $participant, true);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
        return $this->messageManager->markReadByThreadAndParticipant($readable, $participant, false);
    }

    /**
     * {@inheritdoc}
     */
    public function saveThread(ThreadInterface $thread, bool $andFlush = true): void
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
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * Creates a new ThreadMetadata instance.
     */
    private function createThreadMetadata(): ThreadMetadata
    {
        return new $this->metaClass();
    }

    /*
     * DENORMALIZATION
     */

    /**
     * Performs denormalization tricks.
     */
    private function denormalize(Thread $thread): void
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
    private function doParticipants(Thread $thread): void
    {
        foreach ($thread->getMessages() as $message) {
            $thread->addParticipant($message->getSender());
        }
    }

    /**
     * Ensures that metadata exists for each thread participant and that the
     * last message dates are current.
     */
    private function doEnsureThreadMetadataExists(Thread $thread): void
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