<?php

namespace FOS\ChatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\Thread as BaseThread;
use FOS\ChatBundle\Model\ThreadMetadata as ModelThreadMetadata;

abstract class Thread extends BaseThread
{
    /**
     * Messages contained in this thread.
     */
    protected Collection $messages;

    /**
     * Users participating in this conversation.
     */
    protected Collection $participants;

    /**
     * Thread metadata.
     */
    protected Collection $metadata;

    /**
     * All text contained in the thread messages
     * Used for the full text search.
     */
    protected string $keywords = '';

    /**
     * Participant that created the thread.
     */
    protected ?ParticipantInterface $createdBy = null;

    /**
     * Date this thread was created at.
     */
    protected ?\DateTimeImmutable $createdAt = null;

    /**
     * {@inheritdoc}
     */
    public function getParticipants() : array
    {
        return $this->getParticipantsCollection()->toArray();
    }

    /**
     * Gets the users participating in this conversation.
     *
     * Since the ORM schema does not map the participants collection field, it
     * must be created on demand.
     */
    protected function getParticipantsCollection() : ArrayCollection
    {
        if (!isset($this->participants)) 
        {
            $this->participants = new ArrayCollection();

            foreach ($this->metadata as $data) {
                $this->participants->add($data->getParticipant());
            }
        }

        return $this->participants;
    }

    /**
     * {@inheritdoc}
     */
    public function addParticipant(ParticipantInterface $participant): void
    {
        if (!$this->isParticipant($participant)) {
            $this->getParticipantsCollection()->add($participant);
        }
    }

    /**
     * Adds many participants to the thread.
     */
    public function addParticipants(array|\Traversable $participants) : Thread
    {
        foreach ($participants as $participant) {
            $this->addParticipant($participant);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isParticipant(ParticipantInterface $participant): bool
    {
        return $this->getParticipantsCollection()->contains($participant);
    }

    /**
     * @return Collection<int, ModelThreadMetadata>
     */
    public function getAllMetadata() : Collection
    {
        return $this->metadata;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function addMetadata(ModelThreadMetadata $meta): void
    {
        $meta->setThread($this);
        parent::addMetadata($meta);
    }
}
