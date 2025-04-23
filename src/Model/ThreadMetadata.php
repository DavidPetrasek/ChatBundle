<?php

namespace FOS\ChatBundle\Model;

abstract class ThreadMetadata
{
    protected ParticipantInterface $participant;

    protected bool $isDeleted = false;

    /**
     * Date when the message was marked as deleted.
     */
    protected ?\DateTimeImmutable $deletedAt = null;

    protected ?int $participantStatus = null;

    /**
     * Date of last message written by the participant.
     */
    protected \DateTimeImmutable $lastParticipantMessageDate;

    /**
     * Date of last message written by another participant.
     */
    protected \DateTimeImmutable $lastMessageDate;


    public function getParticipant() : ParticipantInterface
    {
        return $this->participant;
    }

    public function setParticipant(ParticipantInterface $participant): self
    {
        $this->participant = $participant;

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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getParticipantStatus(): ?ParticipantStatus
    {
        if (!is_int($this->participantStatus)) {return null;}

        return ParticipantStatus::from($this->participantStatus);
    }

    public function setParticipantStatus(null|int|ParticipantStatus $participantStatus): self
    {
        if ($participantStatus instanceof ParticipantStatus) {$participantStatus = $participantStatus->value;}   
        
        $this->participantStatus = $participantStatus;

        return $this;
    }

    public function getLastParticipantMessageDate() : \DateTimeImmutable
    {
        return $this->lastParticipantMessageDate;
    }

    public function setLastParticipantMessageDate(\DateTimeImmutable $date): self
    {
        $this->lastParticipantMessageDate = $date;

        return $this;
    }

    public function getLastMessageDate() : \DateTimeImmutable
    {
        return $this->lastMessageDate;
    }

    public function setLastMessageDate(\DateTimeImmutable $date): self
    {
        $this->lastMessageDate = $date;

        return $this;
    }
}
