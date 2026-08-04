<?php

namespace FOS\ChatBundle\Document;

use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\Message as BaseMessage;

abstract class Message extends BaseMessage
{
    /**
     * Tells if the message is spam.
     * This denormalizes Thread.spam.
     */
    protected bool $spam = false;

    /**
     * Whether this message was sent by the system and not by a real user.
     */
    protected bool $automaticReply = false;

    /**
     * The unreadForParticipants array will contain a participant's ID if the
     * message is not read by the participant and the message is not spam.
     */
    protected array $unreadForParticipants = [];

    /**
     * @return Collection<int, MessageMetadata>
     */
    public function getAllMetadata(): Collection
    {
        return $this->metadata;
    }

    public function isSpam(): bool
    {
        return $this->spam;
    }

    public function setSpam(bool $spam): self
    {
        $this->spam = $spam;

        return $this;
    }

    public function isAutomaticReply(): bool
    {
        return $this->automaticReply;
    }

    public function setAutomaticReply(bool $automaticReply): self
    {
        $this->automaticReply = $automaticReply;

        return $this;
    }

    public function getUnreadForParticipants(): array
    {
        return $this->unreadForParticipants;
    }

    public function setUnreadForParticipants(array $unreadForParticipants): self
    {
        $this->unreadForParticipants = $unreadForParticipants;

        return $this;
    }

    /*
     * DENORMALIZATION
     */

    public function denormalize(): void
    {
        $this->doSenderRead();
        $this->doEnsureUnreadForParticipantsArray();
    }

    protected function doSenderRead(): void
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