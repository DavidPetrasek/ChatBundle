<?php

namespace FOS\ChatBundle\Model;

use Doctrine\Common\Collections\Collection;

interface ThreadInterface extends ReadableInterface
{
    /**
     * Gets the message unique id.
     */
    public function getId() : ?int;

    public function getSubject() : string;

    public function setSubject(string $subject);

    /**
     * @return Collection<int, MessageInterface>
     */
    public function getMessages() : Collection;

    /**
     * Adds a new message to the thread.
     */
    public function addMessage(MessageInterface $message);

    /**
     * Gets the first message of the thread.
     */
    public function getFirstMessage() : MessageInterface;

    /**
     * Gets the last message of the thread.
     */
    public function getLastMessage() : MessageInterface;

    /**
     * Gets the participant that created the thread
     * Generally the sender of the first message.
     */
    public function getCreatedBy() : ?ParticipantInterface;

    /**
     * Sets the participant that created the thread
     * Generally the sender of the first message.
     */
    public function setCreatedBy(?ParticipantInterface $participant);

    /**
     * Gets the date this thread was created at
     * Generally the date of the first message.
     */
    public function getCreatedAt() : ?\DateTimeImmutable;

    /**
     * Sets the date this thread was created at
     * Generally the date of the first message.
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt);

    /**
     * Gets the users participating in this conversation.
     * @return array<int, ParticipantInterface>
     */
    public function getParticipants() : array;

    /**
     * Tells if the user participates to the conversation.
     */
    public function isParticipant(ParticipantInterface $participant) : bool;

    /**
     * Adds a participant to the thread
     * If it already exists, nothing is done.
     */
    public function addParticipant(ParticipantInterface $participant);

    /**
     * Tells if this thread is deleted by this participant.
     */
    public function isDeletedByParticipant(ParticipantInterface $participant) : bool;

    /**
     * Sets the thread as deleted or not deleted for all participants.
     */
    public function setIsDeleted(bool $isDeleted);

    /**
     * Get the participants this participant is talking with.
     */
    public function getOtherParticipants(ParticipantInterface $participant) : array;

    public function getAllMetadata() : Collection;

    public function getMetadataForParticipant(ParticipantInterface $participant) : ?ThreadMetadata;

    public function addMetadata(ThreadMetadata $meta): void;
}
