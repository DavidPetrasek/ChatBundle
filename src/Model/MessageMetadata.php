<?php

namespace FOS\ChatBundle\Model;

abstract class MessageMetadata
{
    protected ParticipantInterface $participant;

    protected bool $isRead = false;

    /**
     * Date when the message was marked as read.
     */
    protected ?\DateTimeImmutable $readAt = null;

    
    public function getParticipant() : ParticipantInterface
    {
        return $this->participant;
    }

    public function setParticipant(ParticipantInterface $participant): void
    {
        $this->participant = $participant;
    }

    public function getIsRead() : bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): void
    {
        $this->isRead = $isRead;
    }

    public function getReadAt() : ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeImmutable $readAt): void
    {
        $this->readAt = $readAt;
    }
}
