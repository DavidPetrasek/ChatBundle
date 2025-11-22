<?php
namespace App\Tests\Validator;

use FOS\ChatBundle\Validator\SelfRecipient;
use FOS\ChatBundle\Validator\SelfRecipientValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class SelfRecipientValidatorTest extends ConstraintValidatorTestCase
{
    private $participantProvider = null;
    private $participant = null;

    protected function createValidator(): ConstraintValidatorInterface
    {
        $this->participantProvider = $this->getMockBuilder(\FOS\ChatBundle\Security\ParticipantProviderInterface::class)->getMock();
        $this->participant = $this->getMockBuilder(\FOS\ChatBundle\Model\ParticipantInterface::class)->getMock();
        $this->participantProvider->method('getAuthenticatedParticipant')->willReturn($this->participant);

        return new SelfRecipientValidator($this->participantProvider);
    }

    public function testNoViolation(): void
    {
        $this->validator->validate(null, new SelfRecipient());

        $this->assertNoViolation();
    }

    public function testViolation(): void
    {
        $this->validator->validate($this->participantProvider->getAuthenticatedParticipant(), new SelfRecipient());

        $this->buildViolation('fos_chat.self_recipient')
            ->assertRaised();
    }
}