<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Tests\Service\SpamDetection;

use FOS\ChatBundle\Model\ParticipantInterface;
use FOS\ChatBundle\Model\SpamStatus;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use FOS\ChatBundle\Service\SpamDetection\AkismetSpamDetector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class AkismetSpamDetectorTest extends TestCase
{
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->requestStack = new RequestStack();
        // Push a default request so RequestStack never returns null
        $this->requestStack->push(Request::create('http://localhost'));
    }

    public function testCheckReturnsHamWhenApiKeyIsEmpty(): void
    {
        // If the API key is missing, HttpClient must not be called
        $httpClient = new MockHttpClient(function (): void {
            $this->fail('HttpClient should not be called when API key is empty.');
        });

        $detector = $this->createDetector(httpClient: $httpClient, apiKey: '');
        $status = $detector->check('Hello world!');

        $this->assertSame(SpamStatus::HAM, $status);
    }

    public function testCheckReturnsHamForCleanMessage(): void
    {
        // Akismet API returns 'false' = not spam
        $mockResponse = new MockResponse('false');
        $httpClient = new MockHttpClient($mockResponse);

        $detector = $this->createDetector($httpClient);
        $status = $detector->check('A regular, clean message.');

        $this->assertSame(SpamStatus::HAM, $status);
    }

    public function testCheckReturnsSpamForSpamMessage(): void
    {
        // Akismet API returns 'true' = spam
        $mockResponse = new MockResponse('true');
        $httpClient = new MockHttpClient($mockResponse);

        $detector = $this->createDetector($httpClient);
        $status = $detector->check('Buy cheap meds at viagra-test-123!');

        $this->assertSame(SpamStatus::SPAM, $status);
    }

    public function testCheckReturnsDiscardForSevereSpam(): void
    {
        // Akismet API returns 'true' + 'x-akismet-pro-tip: discard' header = severe spam
        $mockResponse = new MockResponse('true', [
            'response_headers' => [
                'x-akismet-pro-tip' => 'discard',
            ],
        ]);
        $httpClient = new MockHttpClient($mockResponse);

        $detector = $this->createDetector($httpClient);
        $status = $detector->check('Severe spam to be discarded immediately');

        $this->assertSame(SpamStatus::DISCARD, $status);
    }

    public function testCheckResolvesSiteUrlFromRequestWhenNull(): void
    {
        // Prepare a specific HTTP request
        $request = Request::create('https://my-domain.com/chat');
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $mockResponse = new MockResponse('false');
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use ($mockResponse) {
            // Verify that the payload contains the dynamically resolved URL
            $this->assertStringContainsString('blog=https%3A%2F%2Fmy-domain.com', $options['body']);

            return $mockResponse;
        });

        $detector = $this->createDetector(
            httpClient: $httpClient,
            siteUrl: null,
            requestStack: $requestStack,
        );

        $status = $detector->check('Hello');

        $this->assertSame(SpamStatus::HAM, $status);
    }

    public function testCheckPassesParticipantDetailsToAkismet(): void
    {
        $participant = new TestParticipant(userIdentifier: 'john_doe_123', email: 'john.doe@example.com');

        $mockResponse = new MockResponse('false');
        $httpClient = new MockHttpClient(function (string $method, string $url, array $options) use ($mockResponse) {
            // Verify author details were included in the request payload
            $this->assertStringContainsString('comment_author=john_doe_123', $options['body']);
            $this->assertStringContainsString('comment_author_email=john.doe%40example.com', $options['body']);

            return $mockResponse;
        });

        $detector = $this->createDetector(httpClient: $httpClient, participant: $participant);
        $detector->check('Test message with participant metadata');
    }

    private function createDetector(
        MockHttpClient $httpClient,
        string $apiKey = 'valid-api-key',
        ?string $siteUrl = 'https://test.com',
        ?RequestStack $requestStack = null,
        ?ParticipantInterface $participant = new TestParticipant(userIdentifier: 'default_user'),
    ): AkismetSpamDetector {
        $participantProvider = $this->createMock(ParticipantProviderInterface::class);
        $participantProvider
            ->method('getAuthenticatedParticipant')
            ->willReturn($participant);

        return new AkismetSpamDetector(
            apiKey: $apiKey,
            siteUrl: $siteUrl,
            httpClient: $httpClient,
            requestStack: $requestStack ?? $this->requestStack,
            participantProvider: $participantProvider,
        );
    }
}

/**
 * Concrete test stub implementing ParticipantInterface for PHPUnit tests.
 */
class TestParticipant implements ParticipantInterface
{
    public function __construct(
        private readonly int|string $id = 1,
        private readonly string $userIdentifier = 'default_user',
        private readonly ?string $email = null,
    ) {}

    public function getId(): int|string
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}