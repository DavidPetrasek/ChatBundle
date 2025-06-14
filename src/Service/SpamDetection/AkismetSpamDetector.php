<?php

namespace FOS\ChatBundle\Service\SpamDetection;

use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Ornicar\AkismetBundle\Akismet\AkismetInterface;

class AkismetSpamDetector implements SpamDetectorInterface
{
    public function __construct
    (
        private readonly AkismetInterface $akismet, 
        private readonly ParticipantProviderInterface $participantProvider
    )
    {}

    /**
     * {@inheritdoc}
     */
    public function isSpam($message): bool
    {
        return $this->akismet->isSpam([
            'comment_author' => (string) $this->participantProvider->getAuthenticatedParticipant(),
            'comment_content' => $message->getBody(),
        ]);
    }
}
