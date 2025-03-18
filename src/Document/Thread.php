<?php

namespace FOS\ChatBundle\Document;

use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\Thread as AbstractThread;

abstract class Thread extends AbstractThread
{
    /**
     * Date that the last message in this thread was created at.
     *
     * This denormalization field is used for sorting threads in the inbox and
     * sent list.
     */
    protected \DateTimeImmutable $lastMessageDate;

    /**
     * All text contained in the thread messages
     * Used for the full text search.
     */
    protected string $keywords = '';

    /**
     * The activeParticipants array is a union of the activeRecipients and
     * activeSenders arrays.
     */
    protected array $activeParticipants = [];

    /**
     * The activeRecipients array will contain a participant's ID if the thread
     * is not deleted for the participant, the thread is not spam and at least
     * one message in the thread is not created by the participant.
     */
    protected array $activeRecipients = [];

    /**
     * The activeSenders array will contain a participant's ID if the thread is
     * not deleted for the participant and at least one message in the thread
     * is created by the participant.
     */
    protected array $activeSenders = [];

    /**
     * Gets the users participating in this conversation.
     */
    public function getParticipants() : array
    {
        return $this->participants->toArray();
    }

    /**
     * Adds a participant to the thread
     * If it already exists, nothing is done.
     */
    public function addParticipant(ParticipantInterface $participant): void
    {
        if (!$this->isParticipant($participant)) {
            $this->participants->add($participant);
        }
    }

    /**
     * Tells if the user participates to the conversation.
     */
    public function isParticipant(ParticipantInterface $participant) : bool
    {
        return $this->participants->contains($participant);
    }

    /*
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks.
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

    /**
     * Ensures that the createdBy & createdAt properties are set.
     */
    protected function doCreatedByAndAt()
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

    /**
     * Ensures that the lastMessageDate property is up to date.
     */
    protected function doLastMessageDate()
    {
        if (!$message = $this->getLastMessage()) {
            return;
        }

        $this->lastMessageDate = $message->getCreatedAt();
    }

    /**
     * Adds all messages contents to the keywords property.
     */
    protected function doKeywords()
    {
        $keywords = $this->getSubject();

        foreach ($this->getMessages() as $message) {
            $keywords .= ' '.$message->getBody();
        }

        // we only need each word once
        $this->keywords = implode(' ', array_unique(str_word_count(mb_strtolower($keywords, 'UTF-8'), 1)));
    }

    /**
     * Denormalizes the value of isSpam to messages.
     */
    protected function doSpam()
    {
        foreach ($this->getMessages() as $message) {
            $message->setIsSpam($this->getIsSpam());
        }
    }

    /**
     * Ensures that metadata last message dates are up to date.
     *
     * Precondition: metadata exists for all thread participants
     */
    protected function doMetadataLastMessageDates()
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

    /**
     * Ensures that active participant, recipient and sender arrays are updated.
     */
    protected function doEnsureActiveParticipantArrays()
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
                } elseif (!$this->getIsSpam()) {
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
