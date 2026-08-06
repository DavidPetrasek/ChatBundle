<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Twig\Extension;

use FOS\ChatBundle\Model\MessageInterface;
use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\Service\Provider\ProviderInterface;
use FOS\ChatBundle\Security\AuthorizerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Twig\Attribute\AsTwigFunction;

class MessageExtension
{
    private ?int $nbUnreadMessagesCache = null;

    public function __construct
    (
        private readonly ParticipantProviderInterface $participantProvider, 
        private readonly ProviderInterface $provider, 
        private readonly AuthorizerInterface $authorizer
    )
    {}

    #[AsTwigFunction('fos_chat_read')]
    public function isRead(ReadableInterface $readable) : bool
    {
        return $readable->isReadByParticipant($this->getAuthenticatedParticipant());
    }

    #[AsTwigFunction('fos_chat_can_delete_thread')]
    public function canDeleteThread(ThreadInterface $thread) : bool
    {
        return $this->authorizer->canDeleteThread($thread);
    }

    #[AsTwigFunction('fos_chat_can_delete_message')]
    public function canDeleteMessage(MessageInterface $message) : bool
    {
        return $this->authorizer->canDeleteMessage($message);
    }

    #[AsTwigFunction('fos_chat_deleted_by_participant')]
    public function isThreadDeletedByParticipant(ThreadInterface $thread) : bool
    {
        return $thread->isDeletedByParticipant($this->getAuthenticatedParticipant());
    }

    #[AsTwigFunction('fos_chat_nb_unread')]
    public function getNbUnread() : int
    {
        if (null === $this->nbUnreadMessagesCache) {
            $this->nbUnreadMessagesCache = $this->provider->getNbUnreadMessages();
        }

        return $this->nbUnreadMessagesCache;
    }

    #[AsTwigFunction('fos_chat_get_participant')]
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }
}
