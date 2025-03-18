<?php

namespace FOS\ChatBundle\Security;

use FOS\ChatBundle\Model\ParticipantInterface;

/**
 * Provides the authenticated participant.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ParticipantProviderInterface
{
    /**
     * Gets the current authenticated user.
     */
    public function getAuthenticatedParticipant() : ParticipantInterface;
}
