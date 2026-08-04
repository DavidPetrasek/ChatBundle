<?php

namespace FOS\ChatBundle\Document;

use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\Thread as AbstractThread;
use FOS\ChatBundle\Model\ThreadMetadata as ModelThreadMetadata;

abstract class Thread extends AbstractThread
{
    /**
     * Date that the last message in this thread was created at.
     */
    protected ?\DateTimeImmutable $lastMessageDate = null;

    protected bool $spam = false;

    /**
     * All text contained in the thread messages.
     * Used for full text search.
     */
    protected string $keywords = '';

    /**
     * Union of activeRecipients and activeSenders.
     */
    protected array $activeParticipants = [];

    /**
     * Contains participant IDs for non-deleted, non-spam threads with incoming messages.
     */
    protected array $activeRecipients = [];

    /**
     * Contains participant IDs for non-deleted threads with sent messages.
     */
    protected array $activeSenders = [];

    /**
     * @return Collection<int, ModelThreadMetadata>
     */
    public function getAllMetadata(): Collection
    {
        return $this->metadata;
    }

    public function getLastMessageDate(): ?\DateTimeImmutable
    {
        return $this->lastMessageDate;
    }

    public function setLastMessageDate(?\DateTimeImmutable $lastMessageDate): self
    {
        $this->lastMessageDate = $lastMessageDate;

        return $this;
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

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getActiveParticipants(): array
    {
        return $this->activeParticipants;
    }

    public function getActiveRecipients(): array
    {
        return $this->activeRecipients;
    }

    public function getActiveSenders(): array
    {
        return $this->activeSenders;
    }

    public function getParticipants(): array
    {
        return $this->participants->toArray();
    }

    public function addParticipant(ParticipantInterface $participant): void
    {
        if (!$this->isParticipant($participant)) {
            $this->participants->add($participant);
        }
    }

    public function isParticipant(ParticipantInterface $participant): bool
    {
        return $this->participants->contains($participant);
    }

    /*
     * DENORMALIZATION
     */

    public function denormalize(): void
    {
        $this->doCreatedByAndAt();
        $this->doLastMessageDate();
        $this->doKeywords();
        $this->doSpam();
        $this->doMetadataLastMessageDates();
        $this->doEnsureActiveParticipantArrays();
    }

    protected function doCreatedByAndAt(): void
    {
        if ($this->getCreatedBy() instanceof ParticipantInterface) {
            return;
        }

        if (!$message = $this->getFirstMessage()) {
            return;
        }

        $this->setCreatedBy($message->getSender());
        $this->setCreatedAt($message->getCreatedAt());
    }

    protected function doLastMessageDate(): void
    {
        if (!$message = $this->getLastMessage()) {
            return;
        }

        $this->lastMessageDate = $message->getCreatedAt();
    }

    protected function doKeywords(): void
    {
        $keywords = $this->getSubject();

        foreach ($this->getMessages() as $message) {
            $keywords .= ' ' . $message->getBody();
        }

        $this->keywords = implode(' ', array_unique(str_word_count(mb_strtolower($keywords, 'UTF-8'), 1)));
    }

    protected function doSpam(): void
    {
        foreach ($this->getMessages() as $message) {
            $message->setSpam($this->isSpam());
        }
    }

    protected function doMetadataLastMessageDates(): void
    {
        foreach ($this->metadata as $meta) {
            foreach ($this->getMessages() as $message) {
                if ($meta->getParticipant()->getId() !== $message->getSender()->getId()) {
                    if (null === $meta->getLastMessageDate() || $meta->getLastMessageDate()->getTimestamp() < $message->getTimestamp()) {
                        $meta->setLastMessageDate($message->getCreatedAt());
                    }
                } elseif (null === $meta->getLastParticipantMessageDate() || $meta->getLastParticipantMessageDate()->getTimestamp() < $message->getTimestamp()) {
                    $meta->setLastParticipantMessageDate($message->getCreatedAt());
                }
            }
        }
    }

    protected function doEnsureActiveParticipantArrays(): void
    {
        $this->activeParticipants = [];
        $this->activeRecipients = [];
        $this->activeSenders = [];

        foreach ($this->getParticipants() as $participant) {
            if ($this->isDeletedByParticipant($participant)) {
                continue;
            }

            $participantIsActiveRecipient = false;
            $participantIsActiveSender = false;

            foreach ($this->getMessages() as $message) {
                if ($message->getSender()->getId() === $participant->getId()) {
                    $participantIsActiveSender = true;
                } elseif (!$this->isSpam()) {
                    $participantIsActiveRecipient = true;
                }

                if ($participantIsActiveRecipient && $participantIsActiveSender) {
                    break;
                }
            }

            if ($participantIsActiveSender) {
                $this->activeSenders[] = $participant->getId();
            }

            if ($participantIsActiveRecipient) {
                $this->activeRecipients[] = $participant->getId();
            }

            if ($participantIsActiveSender || $participantIsActiveRecipient) {
                $this->activeParticipants[] = $participant->getId();
            }
        }
    }
}