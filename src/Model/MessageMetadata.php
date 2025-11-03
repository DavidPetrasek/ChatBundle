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

    protected bool $isDeleted = false;
    
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

    public function getIsRead() : bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

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


    public function getIsDeleted() : bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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
