<?php

namespace FOS\ChatBundle\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Message model.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface MessageInterface extends ReadableInterface
{
    /**
     * Gets the message unique id.
     */
    public function getId() : ?int;

    public function getThread() : ThreadInterface;

    public function setThread(ThreadInterface $thread);

    public function getCreatedAt() : \DateTimeImmutable;

    public function getBody() : string;

    public function setBody(string $body);

    public function getSender() : ParticipantInterface;

    public function setSender(ParticipantInterface $sender);

    public function addMetadata(MessageMetadata $meta): void;

    public function getAllMetadata() : Collection;

    public function getMetadataForParticipant(ParticipantInterface $participant) : ?MessageMetadata;

    /**
     * Tells if this message is deleted by this participant.
     */
    public function isDeletedByParticipant(ParticipantInterface $participant) : bool;

    /**
     * Sets the message as deleted or not deleted for all recipients including the sender.
     */
    public function setIsDeleted(bool $isDeleted);
}
