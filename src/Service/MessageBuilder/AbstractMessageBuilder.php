<?php

namespace FOS\ChatBundle\Service\MessageBuilder;

use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Fluent interface message builder.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class AbstractMessageBuilder
{
    public function __construct
    (
        /**
         * The message we are building.
         */
        protected MessageInterface $message, 
        /**
         * The thread the message goes in.
         */
        protected ThreadInterface $thread
    )
    {
        $this->message->setThread($this->thread);
        $this->thread->addMessage($this->message);
    }

    /**
     * Gets the created message.
     */
    public function getMessage() : MessageInterface
    {
        return $this->message;
    }

    public function setBody(string $body) : static
    {
        $this->message->setBody($body);

        return $this;
    }

    public function setSender(ParticipantInterface $sender) : static
    {
        $this->message->setSender($sender);
        $this->thread->addParticipant($sender);

        return $this;
    }

    /**
     * Sets whether this message is being sent by the system and not by a real user
     */
    public function setAutomaticReply(bool $automatic_reply) : static
    {
        $this->message->setAutomaticReply($automatic_reply);

        return $this;
    }
}
