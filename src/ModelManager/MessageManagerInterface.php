<?php

namespace FOS\ChatBundle\ModelManager;

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
    public function getMessageByThreadQueryBuilder(int|ThreadInterface $thread);

    /**
     * Messages sent by a participant in a particular thread.
     */
    public function getSentMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread);

    /**
     * How many messages were sent by a participant in a particular thread.
     */
    public function getNbSentMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread);

    /**
     * How many messages were sent by a participant in a particular thread.
     */
    public function getNbSentMessageByParticipantAndThread(int|ParticipantInterface $participant, int|ThreadInterface $thread) : int;

    /**
     * Tells how many unread, messages this participant has in a particular thread.
     */
    public function getNbUnreadMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread);

    /**
     * Tells how many unread, messages this participant has in a particular thread.
     */
    public function getNbUnreadMessageByParticipantAndThread(int|ParticipantInterface $participant, int|ThreadInterface $thread) : int;

    /**
     * Get all unread messages this participant has.
     */
    public function getUnreadMessageByParticipantQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Get all unread messages this participant has in a particular thread.
     */
    public function getUnreadMessageByParticipantAndThreadQueryBuilder(int|ParticipantInterface $participant, int|ThreadInterface $thread);

    /**
     * Tells how many unread, messages this participant has.
     */
    public function getNbUnreadMessageByParticipantQueryBuilder(int|ParticipantInterface $participant);

    /**
     * Tells how many unread, messages this participant has.
     */
    public function getNbUnreadMessageByParticipant(int|ParticipantInterface $participant) : int;

    /**
     * Returns the first message of a thread.
     * This is an optimized version of getFirstMessage() which relies on loading the entire collection.
     */
    public function getFirstMessageByThread(int|ThreadInterface $thread) : ?MessageInterface;

    /**
     * Returns the first message of a thread.
     * This is an optimized version of getFirstMessage() which relies on loading the entire collection.
     */
    public function getFirstMessageByThreadQueryBuilder(int|ThreadInterface $thread);

    /**
     * Returns the last message of a thread.
     * This is an optimized version of getLastMessage() which relies on loading the entire collection.
     */
    public function getLastMessageByThread(int|ThreadInterface $thread) : ?MessageInterface;

    /**
     * Returns the last message of a thread.
     * This is an optimized version of getLastMessage() which relies on loading the entire collection.
     */
    public function getLastMessageByThreadQueryBuilder(int|ThreadInterface $thread);

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
