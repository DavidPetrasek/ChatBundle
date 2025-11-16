<?php

namespace FOS\ChatBundle\Validator;

use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SelfRecipientValidator extends ConstraintValidator
{
    public function __construct
    (
        private readonly ParticipantProviderInterface $participantProvider
    )
    {}

    public function validate(mixed $recipient, Constraint $constraint): void
    {
        if ($recipient === $this->participantProvider->getAuthenticatedParticipant()) 
        {
            $this->context->addViolation($constraint->message);
        }
    }
}
