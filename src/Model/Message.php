<?php

namespace FOS\ChatBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\MessageMetadata;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Abstract message model.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Message implements MessageInterface
{
    /**
     * Unique id of the message.
     */
    protected ?int $id = null;

    /**
     * Thread the message belongs to.
     */
    protected ThreadInterface $thread;

    /**
     * User who sent the message.
     */
    protected ParticipantInterface $sender;

    /**
     * Text body of the message.
     */
    protected string $body;

    /**
     * Date when the message was sent.
     */
    protected \DateTimeImmutable $createdAt;

    /**
     * Whether this message was sent by the system and not by a real user
     */
    protected bool $automatic_reply = false;

    /**
     * Collection of MessageMetadata.
     */
    protected Collection $metadata;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->metadata = new ArrayCollection();
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
    public function getThread(): ThreadInterface
    {
        return $this->thread;
    }

    /**
     * {@inheritdoc}
     */
    public function setThread(ThreadInterface $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function setBody($body): self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSender(): ParticipantInterface
    {
        return $this->sender;
    }

    /**
     * {@inheritdoc}
     */
    public function setSender(ParticipantInterface $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Gets the created at timestamp.
     */
    public function getTimestamp() : int
    {
        return $this->getCreatedAt()->getTimestamp();
    }

    /**
     * Adds MessageMetadata to the metadata collection.
     */
    public function addMetadata(MessageMetadata $meta): void
    {
        $this->metadata->add($meta);
    }

    /**
     * Get the MessageMetadata for a participant.
     */
    public function getMetadataForParticipant(ParticipantInterface $participant) : ?MessageMetadata
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
    public function isReadByParticipant(ParticipantInterface $participant): bool
    {
        if (($meta = $this->getMetadataForParticipant($participant)) instanceof MessageMetadata) {
            return $meta->getIsRead();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead): self
    {
        if (!($meta = $this->getMetadataForParticipant($participant)) instanceof MessageMetadata) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsRead($isRead);
        $meta->setReadAt($isRead ? new \DateTimeImmutable() : null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeletedByParticipant(ParticipantInterface $participant): bool
    {
        if (($meta = $this->getMetadataForParticipant($participant)) instanceof MessageMetadata) {
            return $meta->getIsDeleted();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeletedByParticipant(ParticipantInterface $participant, $isDeleted): self
    {
        if (!($meta = $this->getMetadataForParticipant($participant)) instanceof MessageMetadata) {
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
        foreach ($this->metadata as $meta) 
        {
            $meta->setIsDeleted($isDeleted);
            $meta->setDeletedAt($isDeleted ? new \DateTimeImmutable() : null);
        }
        
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAutomaticReply(): bool
    {
        return $this->automatic_reply;
    }

    /**
     * {@inheritdoc}
     */
    public function setAutomaticReply(bool $automatic_reply): self
    {
        $this->automatic_reply = $automatic_reply;

        return $this;
    }
}
