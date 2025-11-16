<?php

namespace FOS\ChatBundle\Service\Reader;

use FOS\ChatBundle\Event\FOSChatEvents;
use FOS\ChatBundle\Event\ReadableEvent;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\ModelManager\ReadableManagerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Marks messages and threads as read or unread.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Reader implements ReaderInterface
{
    public function __construct(
        /**
         * The participantProvider instance.
         */
        private readonly ParticipantProviderInterface $participantProvider,
        /**
         * The readable manager.
         */
        private readonly ReadableManagerInterface $readableManager,
        /**
         * The event dispatcher.
         */
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function markAsRead(ReadableInterface $readable): void
    {
        $participant = $this->getAuthenticatedParticipant();
        if ($readable->isReadByParticipant($participant)) {
            return;
        }

        $this->readableManager->markAsReadByParticipant($readable, $participant);

        $this->dispatcher->dispatch(new ReadableEvent($readable), FOSChatEvents::POST_READ);
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnread(ReadableInterface $readable): void
    {
        $participant = $this->getAuthenticatedParticipant();
        if (!$readable->isReadByParticipant($participant)) {
            return;
        }

        $this->readableManager->markAsUnreadByParticipant($readable, $participant);

        $this->dispatcher->dispatch(new ReadableEvent($readable), FOSChatEvents::POST_UNREAD);
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
