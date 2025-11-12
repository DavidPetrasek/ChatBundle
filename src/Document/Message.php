<?php

namespace FOS\ChatBundle\Document;

use FOS\ChatBundle\Model\Message as BaseMessage;

abstract class Message extends BaseMessage
{
    /**
     * Tells if the message is spam
     * This denormalizes Thread.spam.
     */
    protected bool $spam = false;

    /**
     * The unreadForParticipants array will contain a participant's ID if the
     * message is not read by the participant and the message is not spam.
     */
    protected array $unreadForParticipants = [];

    public function setSpam(bool $spam): self
    {
        $this->spam = $spam;

        return $this;
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks.
     */
    public function denormalize(): void
    {
        $this->doSenderRead();
        $this->doEnsureUnreadForParticipantsArray();
    }

    /**
     * Ensures that the sender is considered to have read this message.
     */
    protected function doSenderRead()
    {
        $this->setReadByParticipant($this->getSender(), true);
    }

    /**
     * Ensures that the unreadForParticipants array is updated.
     */
    protected function doEnsureUnreadForParticipantsArray()
    {
        $this->unreadForParticipants = [];

        if ($this->spam) {
            return;
        }

        foreach ($this->metadata as $metadata) {
            if (!$metadata->isRead()) {
                $this->unreadForParticipants[] = $metadata->getParticipant()->getId();
            }
        }
    }
}
