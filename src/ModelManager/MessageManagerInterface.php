<?php

namespace FOS\ChatBundle\ModelManager;

use Doctrine\ORM\QueryBuilder;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Interface to be implemented by message managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to messages should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MessageManagerInterface extends ReadableManagerInterface
{
    /**
     * Returns a query builder to get all messages in a thread.
     */
    public function getMessageByThreadQueryBuilder(int|ThreadInterface $thread) : QueryBuilder;

    /**
     * How many messages were sent by a participant in a particular thread.
     */
    public function getNbSentMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread) : QueryBuilder;

    /**
     * How many messages were sent by a participant in a particular thread.
     */
    public function getNbSentMessageByParticipantAndThread(ParticipantInterface $participant, ThreadInterface $thread) : int;

    /**
     * Tells how many unread, non-spam, messages this participant has in a particular thread.
     */
    public function getNbUnreadMessageByParticipantAndThreadQueryBuilder(ParticipantInterface $participant, ThreadInterface $thread) : QueryBuilder;

    /**
     * Tells how many unread, non-spam, messages this participant has in a particular thread.
     */
    public function getNbUnreadMessageByParticipantAndThread(ParticipantInterface $participant, ThreadInterface $thread) : int;

    /**
     * Get all unread messages this participant has.
     */
    public function getUnreadMessageByParticipantQueryBuilder(ParticipantInterface $participant): QueryBuilder;

    /**
     * Tells how many unread, non-spam, messages this participant has.
     */
    public function getNbUnreadMessageByParticipantQueryBuilder(ParticipantInterface $participant) : QueryBuilder;

    /**
     * Tells how many unread, non-spam, messages this participant has.
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant) : int;

    /**
     * Returns the first message of a thread.
     * This is an optimized version of getFirstMessage() which relies on loading the entire collection.
     */
    public function getFirstMessageByThread(ThreadInterface $thread) : ?MessageInterface;

    /**
     * Returns the first message of a thread.
     * This is an optimized version of getFirstMessage() which relies on loading the entire collection.
     */
    public function getFirstMessageByThreadQueryBuilder(ThreadInterface $thread) : QueryBuilder;

    /**
     * Returns the last message of a thread.
     * This is an optimized version of getLastMessage() which relies on loading the entire collection.
     */
    public function getLastMessageByThread(ThreadInterface $thread) : ?MessageInterface;

    /**
     * Returns the last message of a thread.
     * This is an optimized version of getLastMessage() which relies on loading the entire collection.
     */
    public function getLastMessageByThreadQueryBuilder(ThreadInterface $thread) : QueryBuilder;

    /**
     * Creates an empty message instance.
     */
    public function createMessage() : MessageInterface;

    /**
     * Saves a message.
     */
    public function saveMessage(MessageInterface $message, bool $andFlush = true);

    /**
     * Returns the message's fully qualified class.
     */
    public function getClass() : string;
}
