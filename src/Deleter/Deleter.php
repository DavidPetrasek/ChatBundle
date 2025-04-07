<?php

namespace FOS\ChatBundle\Deleter;

use FOS\ChatBundle\Event\FOSMessageEvents;
use FOS\ChatBundle\Event\ThreadEvent;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\Security\AuthorizerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Marks threads as deleted.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Deleter implements DeleterInterface
{
    public function __construct(
        /**
         * The authorizer instance.
         */
        private readonly AuthorizerInterface $authorizer,
        /**
         * The participant provider instance.
         */
        private readonly ParticipantProviderInterface $participantProvider,
        /**
         * The event dispatcher.
         */
        private readonly \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function markAsDeleted(ThreadInterface $thread): void
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }

        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), true);

        $this->dispatcher->dispatch(new ThreadEvent($thread), FOSMessageEvents::POST_DELETE);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUndeleted(ThreadInterface $thread): void
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }

        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), false);

        $this->dispatcher->dispatch(new ThreadEvent($thread), FOSMessageEvents::POST_UNDELETE);
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
