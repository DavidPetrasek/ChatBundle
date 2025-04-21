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


    public function getIsDeleted() : bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getDeletedAt() : ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
