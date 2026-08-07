<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Service\SpamDetection;

use FOS\ChatBundle\Model\SpamStatus;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Omines\Akismet\Akismet;
use Omines\Akismet\AkismetMessage;
use Omines\Akismet\MessageType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AkismetSpamDetector implements SpamDetectorInterface
{
    public function __construct
    (
        private readonly string $apiKey,
        private readonly ?string $siteUrl,
        private readonly HttpClientInterface $httpClient,
        private readonly RequestStack $requestStack,
        private readonly ParticipantProviderInterface $participantProvider,
    )
    {}

    public function check(string $message): SpamStatus
    {
        if (empty($this->apiKey)) return SpamStatus::HAM;

        $request = $this->requestStack->getCurrentRequest();
        $siteUrl = $this->siteUrl;

        if (empty($siteUrl) && null !== $request) 
        {
            $siteUrl = $request->getSchemeAndHttpHost();
        }
        if (empty($siteUrl)) 
        {
            $siteUrl = 'http://localhost';
        }

        $akismetMessage = AkismetMessage::fromRequest($request)
            ->setContent($message)
            ->setType(MessageType::MESSAGE);

        $participant = $this->participantProvider->getAuthenticatedParticipant();
        if (!empty($participant))
        {
            $akismetMessage->setAuthor($participant->getUserIdentifier());

            if (method_exists($participant, 'getEmail') && !empty($participant->getEmail()))
            {
                $akismetMessage->setAuthorEmail($participant->getEmail());
            }
        }

        $akismet = new Akismet($this->httpClient, $this->apiKey, $siteUrl);
        $response = $akismet->check($akismetMessage);

        if ($response->isSpam()) 
        {
            return $response->shouldDiscard() ? SpamStatus::DISCARD : SpamStatus::SPAM;
        }

        return SpamStatus::HAM;
    }
}