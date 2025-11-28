<?php

namespace FOS\ChatBundle\ModelManager;

use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Interface to be implemented by comment thread managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comment threads should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ThreadManagerInterface extends ReadableManagerInterface
{
    /**
     * Finds a thread by its ID.
     */
    public function findThreadById(int $id) : ?ThreadInterface;

    /**
     * Finds all threads in which a participant is involved.
     */
    public function getParticipantThreadsQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Finds in how many threads a participant is involved.
     */
    public function getNbParticipantThreadsQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Finds not deleted threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     */
    public function getParticipantInboxThreadsQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Finds not deleted threads for a participant,
     * containing at least one message not written by this participant,
     * ordered by last message not written by this participant in reverse order.
     * In one word: an inbox.
     */
    public function findParticipantInboxThreads(int|ParticipantInterface $participant) : array;

    /**
     * Finds not deleted threads from a participant,
     * containing at least one message written by this participant,
     * ordered by last message written by this participant in reverse order.
     * In one word: an sentbox.
     */
    public function getParticipantSentThreadsQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Finds not deleted threads from a participant,
     * containing at least one message written by this participant,
     * ordered by last message written by this participant in reverse order.
     * In one word: an sentbox.
     */
    public function findParticipantSentThreads(int|ParticipantInterface $participant) : array;

    /**
     * Finds deleted threads from a participant,
     * ordered by last message date.
     */
    public function getParticipantDeletedThreadsQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Finds deleted threads from a participant,
     * ordered by last message date.
     */
    public function findParticipantDeletedThreads(int|ParticipantInterface $participant) : array;

    /**
     * Finds not deleted threads for a participant,
     * matching the given search term
     * ordered by last message not written by this participant in reverse order.
     */
    public function getParticipantThreadsBySearchQueryBuilder(int|ParticipantInterface $participant, string $search);

    /**
     * Finds not deleted threads for a participant,
     * matching the given search term
     * ordered by last message not written by this participant in reverse order.
     */
    public function findParticipantThreadsBySearch(int|ParticipantInterface $participant, string $search) : array;

    /**
     * Gets threads created by a participant.
     */
    public function getThreadsCreatedByParticipantQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Gets threads created by a participant.
     */
    public function findThreadsCreatedBy(int|ParticipantInterface $participant) : array;

    /**
     * Creates an empty comment thread instance.
     */
    public function createThread() : ThreadInterface;

    /**
     * Saves a thread.
     */
    public function saveThread(ThreadInterface $thread, bool $andFlush = true) : void;

    /**
     * Deletes a thread
     * This is not participant deletion but real deletion.
     */
    public function deleteThread(ThreadInterface $thread) : void;

    /**
     * Returns the thread's fully qualified class
     */
    public function getClass() : string;
}
