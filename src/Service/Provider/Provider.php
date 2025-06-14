<?php

namespace FOS\ChatBundle\Service\Provider;

use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\ModelManager\MessageManagerInterface;
use FOS\ChatBundle\ModelManager\ThreadManagerInterface;
use FOS\ChatBundle\Security\AuthorizerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Provides threads for the current authenticated user.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Provider implements ProviderInterface
{
    public function __construct
    (
        private readonly ThreadManagerInterface $threadManager, 
        private readonly MessageManagerInterface $messageManager,
        private readonly AuthorizerInterface $authorizer, 
        private readonly ParticipantProviderInterface $participantProvider
    )
    {}

    /**
     * {@inheritdoc}
     */
    public function getInboxThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->findParticipantInboxThreads($participant);
    }

    /**
     * {@inheritdoc}
     */
    public function getSentThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->findParticipantSentThreads($participant);
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->threadManager->findParticipantDeletedThreads($participant);
    }

    /**
     * {@inheritdoc}
     */
    public function getThread($threadId): ThreadInterface
    {
        $thread = $this->threadManager->findThreadById($threadId);
        if (!$thread instanceof ThreadInterface) {
            throw new NotFoundHttpException('There is no such thread');
        }

        if (!$this->authorizer->canSeeThread($thread)) {
            throw new AccessDeniedException('You are not allowed to see this thread');
        }

        return $thread;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbUnreadMessages(): int
    {
        return $this->messageManager->getNbUnreadMessageByParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
