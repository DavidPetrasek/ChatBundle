<?php

namespace FOS\ChatBundle\Model;

abstract class ThreadMetadata
{
    protected ParticipantInterface $participant;

    protected bool $isDeleted = false;

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

    public function setParticipant(ParticipantInterface $participant): void
    {
        $this->participant = $participant;
    }

    public function getIsDeleted() : bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
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

    public function setLastParticipantMessageDate(\DateTimeImmutable $date): void
    {
        $this->lastParticipantMessageDate = $date;
    }

    public function getLastMessageDate() : \DateTimeImmutable
    {
        return $this->lastMessageDate;
    }

    public function setLastMessageDate(\DateTimeImmutable $date): void
    {
        $this->lastMessageDate = $date;
    }
}
