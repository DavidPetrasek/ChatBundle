<?php

namespace FOS\ChatBundle\Security;

use FOS\ChatBundle\Model\ParticipantInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Provides the authenticated participant.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ParticipantProvider implements ParticipantProviderInterface
{
    public function __construct
    (
        private readonly Security $security
    )
    {}

    /**
     * {@inheritdoc}
     */
    public function getAuthenticatedParticipant(): ParticipantInterface
    {
        $participant = $this->security->getUser();

        if (!$participant instanceof ParticipantInterface) {
            throw new AccessDeniedException('Must be logged in with a ParticipantInterface instance');
        }

        return $participant;
    }
}
