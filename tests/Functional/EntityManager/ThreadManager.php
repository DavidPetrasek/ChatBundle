<?php

declare(strict_types=1);

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

    public function getParticipantInboxThreadsQueryBuilder(int|ParticipantInterface $participant)
    {

    }

    public function findParticipantInboxThreads(int|ParticipantInterface $participant): array
    {
        return [new Thread()];
    }

    public function getParticipantSentThreadsQueryBuilder(int|ParticipantInterface $participant)
    {
    }

    public function findParticipantSentThreads(int|ParticipantInterface $participant): array
    {
        return [];
    }

    public function getParticipantDeletedThreadsQueryBuilder(int|ParticipantInterface $participant)
    {
    }

    public function findParticipantDeletedThreads(int|ParticipantInterface $participant): array
    {
        return [];
    }

    public function getParticipantThreadsBySearchQueryBuilder(int|ParticipantInterface $participant, $search)
    {
    }

    public function findParticipantThreadsBySearch(int|ParticipantInterface $participant, $search): array
    {
        return [];
    }

    public function findThreadsCreatedBy(int|ParticipantInterface $participant): array
    {
        return [];
    }

    // Added stubs for abstract methods from BaseThreadManager to satisfy implementation
    public function getParticipantThreadsQueryBuilder(int|ParticipantInterface $participant) : QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in test ThreadManager.');
    }

    public function getNbParticipantThreadsQueryBuilder(int|ParticipantInterface $participant) : QueryBuilder
    {
        throw new \BadMethodCallException('Not implemented in test ThreadManager.');
    }

    public function getThreadsCreatedByParticipantQueryBuilder(int|ParticipantInterface $participant) : QueryBuilder
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
