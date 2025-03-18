<?php

namespace FOS\ChatBundle\ModelManager;

use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;

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
     * Tells how many unread, non-spam, messages this participant has.
     */
    public function getNbUnreadMessageByParticipant(ParticipantInterface $participant) : int;

    /**
     * Creates an empty message instance.
     */
    public function createMessage() : MessageInterface;

    /**
     * Saves a message.
     */
    public function saveMessage(MessageInterface $message, bool $andFlush = true);

    /**
     * Returns the message's fully qualified class MessageManagerInterface.
     */
    public function getClass() : string;
}
