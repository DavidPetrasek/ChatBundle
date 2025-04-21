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
     * Thread the message belongs to.
     */
    protected ThreadInterface $thread;

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
    public function setThread(ThreadInterface $thread): void
    {
        $this->thread = $thread;
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
    public function setBody($body): void
    {
        $this->body = $body;
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
    public function setSender(ParticipantInterface $sender): void
    {
        $this->sender = $sender;
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
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead): void
    {
        if (!($meta = $this->getMetadataForParticipant($participant)) instanceof MessageMetadata) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsRead($isRead);
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
    public function setIsDeletedByParticipant(ParticipantInterface $participant, $isDeleted): void
    {
        if (!($meta = $this->getMetadataForParticipant($participant)) instanceof MessageMetadata) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsDeleted($isDeleted);
        $meta->setDeletedAt($isDeleted ? new \DateTimeImmutable() : null);

        if ($isDeleted) {
            // also mark this message as read
            $meta->setIsRead(true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIsDeleted($isDeleted): void
    {
        foreach ($this->metadata as $meta) 
        {
            $meta->setIsDeleted($isDeleted);
            $meta->setDeletedAt($isDeleted ? new \DateTimeImmutable() : null);

            if ($isDeleted) {
                // also mark this message as read
                $meta->setIsRead(true);
            }
        }
    }
}
