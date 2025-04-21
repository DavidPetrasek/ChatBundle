<?php

namespace FOS\ChatBundle\Deleter;

use FOS\ChatBundle\Event\FOSMessageEvents;
use FOS\ChatBundle\Event\ReadableEvent;
use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\Security\AuthorizerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Marks readables as deleted.
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
    public function markAsDeleted(ReadableInterface $readable): void
    {
        if ($readable instanceof ThreadInterface  &&  !$this->authorizer->canDeleteThread($readable)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        else if ($readable instanceof MessageInterface  &&  !$this->authorizer->canDeleteMessage($readable)) {
            throw new AccessDeniedException('You are not allowed to delete this message');
        }

        $readable->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), true);

        $this->dispatcher->dispatch(new ReadableEvent($readable), FOSMessageEvents::POST_DELETE);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUndeleted(ReadableInterface $readable): void
    {
        if ($readable instanceof ThreadInterface  &&  !$this->authorizer->canDeleteThread($readable)) {
            throw new AccessDeniedException('You are not allowed to undelete this thread');
        }
        else if ($readable instanceof MessageInterface  &&  !$this->authorizer->canDeleteMessage($readable)) {
            throw new AccessDeniedException('You are not allowed to undelete this message');
        }

        $readable->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), false);

        $this->dispatcher->dispatch(new ReadableEvent($readable), FOSMessageEvents::POST_UNDELETE);
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
