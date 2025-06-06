<?php

namespace FOS\ChatBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ThreadMetadata;

/**
 * Abstract thread model.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Thread implements ThreadInterface
{
    /**
     * Unique id of the thread.
     */
    protected ?int $id = null;

    /**
     * Text subject of the thread.
     */
    protected string $subject;

    /**
     * Tells if the thread is spam or flood.
     */
    protected bool $isSpam = false;

    /**
     * Messages contained in this thread.
     */
    protected Collection $messages;

    /**
     * Thread metadata.
     */
    protected Collection $metadata;

    /**
     * Users participating in this conversation.
     */
    protected Collection $participants;

    /**
     * Date this thread was created at.
     */
    protected ?\DateTimeImmutable $createdAt = null;

    /**
     * Participant that created the thread.
     */
    protected ?ParticipantInterface $createdBy = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBy(): ?ParticipantInterface
    {
        return $this->createdBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedBy(?ParticipantInterface $participant): self
    {
        $this->createdBy = $participant;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getIsSpam() : bool
    {
        return $this->isSpam;
    }

    public function setIsSpam(bool $isSpam): self
    {
        $this->isSpam = $isSpam;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage(MessageInterface $message): void
    {
        $this->messages->add($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstMessage(): MessageInterface
    {
        return $this->getMessages()->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getLastMessage(): MessageInterface
    {
        return $this->getMessages()->last();
    }

    /**
     * {@inheritdoc}
     */
    public function isDeletedByParticipant(ParticipantInterface $participant): bool
    {
        if (($meta = $this->getMetadataForParticipant($participant)) instanceof ThreadMetadata) {
            return $meta->getIsDeleted();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeletedByParticipant(ParticipantInterface $participant, $isDeleted): self
    {
        if (!($meta = $this->getMetadataForParticipant($participant)) instanceof ThreadMetadata) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsDeleted($isDeleted);
        $meta->setDeletedAt($isDeleted ? new \DateTimeImmutable() : null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeleted($isDeleted): self
    {
        foreach ($this->getParticipants() as $participant) {
            $this->setIsDeletedByParticipant($participant, $isDeleted);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadByParticipant(ParticipantInterface $participant): bool
    {
        foreach ($this->getMessages() as $message) {
            if (!$message->isReadByParticipant($participant)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead): self
    {
        foreach ($this->getMessages() as $message) {
            $message->setIsReadByParticipant($participant, $isRead);
        }

        return $this;
    }

    /**
     * Adds ThreadMetadata to the metadata collection.
     */
    public function addMetadata(ThreadMetadata $meta): void
    {
        $this->metadata->add($meta);
    }

    /**
     * Gets the ThreadMetadata for a participant.
     */
    public function getMetadataForParticipant(ParticipantInterface $participant) : ?ThreadMetadata
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getOtherParticipants(ParticipantInterface $participant): array
    {
        $otherParticipants = $this->getParticipants();

        $key = array_search($participant, $otherParticipants, true);

        if (false !== $key) {
            unset($otherParticipants[$key]);
        }

        // we want to reset the array indexes
        return array_values($otherParticipants);
    }
}
