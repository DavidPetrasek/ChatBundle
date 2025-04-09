<?php

namespace FOS\ChatBundle\Twig\Extension;

use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\ReadableInterface;
use FOS\ChatBundle\Model\ThreadInterface;
use FOS\ChatBundle\Provider\ProviderInterface;
use FOS\ChatBundle\Security\AuthorizerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessageExtension extends AbstractExtension
{
    private ?int $nbUnreadMessagesCache = null;

    public function __construct(private readonly ParticipantProviderInterface $participantProvider, private readonly ProviderInterface $provider, private readonly AuthorizerInterface $authorizer)
    {
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getFunctions()
    {
        return [
            new TwigFunction('fos_chat_is_read', $this->isRead(...)),
            new TwigFunction('fos_chat_nb_unread', $this->getNbUnread(...)),
            new TwigFunction('fos_chat_can_delete_thread', $this->canDeleteThread(...)),
            new TwigFunction('fos_chat_deleted_by_participant', $this->isThreadDeletedByParticipant(...)),
        ];
    }

    /**
     * Tells if this readable (thread or message) is read by the current user. 
     */
    public function isRead(ReadableInterface $readable) : bool
    {
        return $readable->isReadByParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Checks if the participant can mark a thread as deleted.
     */
    public function canDeleteThread(ThreadInterface $thread) : bool
    {
        return $this->authorizer->canDeleteThread($thread);
    }

    /**
     * Checks if the participant has marked the thread as deleted.
     */
    public function isThreadDeletedByParticipant(ThreadInterface $thread) : bool
    {
        return $thread->isDeletedByParticipant($this->getAuthenticatedParticipant());
    }

    /**
     * Gets the number of unread messages for the current user.
     */
    public function getNbUnread() : int
    {
        if (null === $this->nbUnreadMessagesCache) {
            $this->nbUnreadMessagesCache = $this->provider->getNbUnreadMessages();
        }

        return $this->nbUnreadMessagesCache;
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant() : ParticipantInterface
    {
        return $this->participantProvider->getAuthenticatedParticipant();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'fos_chat';
    }
}
