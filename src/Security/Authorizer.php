<?php

namespace FOS\ChatBundle\Security;

use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;

/**
 * Manages permissions to manipulate threads and messages.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Authorizer implements AuthorizerInterface
{
    public function __construct(private readonly ParticipantProviderInterface $participantProvider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function canSeeThread(ThreadInterface $thread): bool
    {
        return $this->getAuthenticatedParticipant() && $thread->isParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * {@inheritdoc}
     */
    public function canDeleteThread(ThreadInterface $thread): bool
    {
        return $this->canSeeThread($thread);
    }

    /**
     * {@inheritdoc}
     */
    public function canDeleteMessage(MessageInterface $message): bool
    {
        $authenticatedParticipant = $this->getAuthenticatedParticipant();
        return $this->getAuthenticatedParticipant() && $message->getSender() === $authenticatedParticipant;
    }

    /**
     * {@inheritdoc}
     */
    public function canMessageParticipant(ParticipantInterface $participant): bool
    {
        return true;
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
