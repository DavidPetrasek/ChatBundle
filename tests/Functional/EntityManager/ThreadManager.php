<?php

namespace FOS\ChatBundle\Tests\Functional\EntityManager;

use Doctrine\ORM\QueryBuilder;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\ModelManager\ThreadManager as BaseThreadManager;
use FOS\ChatBundle\Tests\Functional\Entity\Thread;

class ThreadManager extends BaseThreadManager
{
    public function findThreadById($id): \FOS\ChatBundle\Tests\Functional\Entity\Thread
    {
        return new Thread();
    }

    public function getParticipantInboxThreadsQueryBuilder(ParticipantInterface $participant)
    {

    }

    public function findParticipantInboxThreads(ParticipantInterface $participant): array
    {
        return [new Thread()];
    }

    public function getParticipantSentThreadsQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function findParticipantSentThreads(ParticipantInterface $participant): array
    {
        return [];
    }

    public function getParticipantDeletedThreadsQueryBuilder(ParticipantInterface $participant)
    {
    }

    public function findParticipantDeletedThreads(ParticipantInterface $participant): array
    {
        return [];
    }

    public function getParticipantThreadsBySearchQueryBuilder(ParticipantInterface $participant, $search)
    {
    }

    public function findParticipantThreadsBySearch(ParticipantInterface $participant, $search): array
    {
        return [];
    }

    public function findThreadsCreatedBy(ParticipantInterface $participant): array
    {
        return [];
    }

    // Added stubs for abstract methods from BaseThreadManager to satisfy implementation
    public function getParticipantThreadsQueryBuilder(ParticipantInterface $participant) : QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in test ThreadManager.');
    }

    public function getNbParticipantThreadsQueryBuilder(ParticipantInterface $participant) : QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in test ThreadManager.');
    }

    public function getThreadsCreatedByParticipantQueryBuilder(ParticipantInterface $participant) : QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in test ThreadManager.');
    }

    public function markAsReadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function markAsUnreadByParticipant(ReadableInterface $readable, ParticipantInterface $participant)
    {
    }

    public function saveThread(ThreadInterface $thread, $andFlush = true) : void
    {
    }

    public function deleteThread(ThreadInterface $thread) : void
    {
    }

    public function getClass(): string
    {
        return Thread::class;
    }
}
