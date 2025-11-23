<?php
namespace App\Tests\Validator;

use FOS\ChatBundle\Validator\ReplyAuthorization;
use FOS\ChatBundle\Validator\ReplyAuthorizationValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class ReplyAuthorizationValidatorTest extends ConstraintValidatorTestCase
{
    private $authorizer = null;
    private $participantProvider = null;
    private $sender = null;
    private $recipient = null;
    private $thread = null;
    private $message = null;

    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->authorizer = $this->getMockBuilder(\FOS\ChatBundle\Security\AuthorizerInterface::class)->getMock();
        $this->participantProvider = $this->getMockBuilder(\FOS\ChatBundle\Security\ParticipantProviderInterface::class)->getMock();

        return new ReplyAuthorizationValidator($this->authorizer, $this->participantProvider);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->sender = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)->getMock();
        $this->recipient = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)->getMock();
        $this->thread = $this->getMockBuilder(\FOS\ChatBundle\Model\ThreadInterface::class)->getMock();
        $this->message = $this->getMockBuilder(\FOS\ChatBundle\Model\MessageInterface::class)->getMock();
    }

    public function testNoViolation(): void
    {
        $this->message->method('getThread')->willReturn($this->thread);
        $this->thread->method('getOtherParticipants')->willReturn([$this->recipient]);

        $this->participantProvider->method('getAuthenticatedParticipant')->willReturn($this->sender);
        $this->authorizer->method('canMessageParticipant')->with($this->recipient)->willReturn(true);

        $this->validator->validate($this->message, new ReplyAuthorization());

        $this->assertNoViolation();
    }

    public function testViolation(): void
    {
        $this->message->method('getThread')->willReturn($this->thread);
        $this->thread->method('getOtherParticipants')->willReturn([$this->recipient]);

        $this->participantProvider->method('getAuthenticatedParticipant')->willReturn($this->sender);
        $this->authorizer->method('canMessageParticipant')->with($this->recipient)->willReturn(false);

        $this->validator->validate($this->message, new ReplyAuthorization());

        $this->buildViolation('fos_chat.reply_not_authorized')
            ->assertRaised();
    }
}
