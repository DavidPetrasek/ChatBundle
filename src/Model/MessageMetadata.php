<?php

namespace FOS\ChatBundle\Model;

abstract class MessageMetadata
{
    protected ParticipantInterface $participant;

    protected bool $read = false;

    /**
     * Date when the message was marked as read.
     */
    protected ?\DateTimeImmutable $readAt = null;

    protected bool $deleted = false;
    
    /**
     * Date when the message was marked as deleted.
     */
    protected ?\DateTimeImmutable $deletedAt = null;


    public function getParticipant() : ParticipantInterface
    {
        return $this->participant;
    }

    public function setParticipant(ParticipantInterface $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function isRead() : bool
    {
        return $this->read;
    }

    public function setRead(bool $read): self
    {
        $this->read = $read;

        return $this;
    }

    public function getReadAt() : ?\DateTimeImmutable
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTimeImmutable $readAt): self
    {
        $this->readAt = $readAt;

        return $this;
    }


    public function isDeleted() : bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getDeletedAt() : ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
