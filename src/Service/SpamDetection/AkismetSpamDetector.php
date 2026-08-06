<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Service\SpamDetection;

use FOS\ChatBundle\Security\ParticipantProviderInterface;

class AkismetSpamDetector implements SpamDetectorInterface
{
    public function __construct
    (
        // private readonly  $, //TODO: implement
        private readonly ParticipantProviderInterface $participantProvider
    )
    {}

    public function isSpam($message): bool
    {
        // TODO: implement
        return false;
    }
}
