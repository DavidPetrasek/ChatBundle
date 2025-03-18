<?php

namespace FOS\ChatBundle\Validator;

use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SelfRecipientValidator extends ConstraintValidator
{
    public function __construct(private ParticipantProviderInterface $participantProvider)
    {
    }

    /**
     * Indicates whether the constraint is valid.
     */
    public function validate(object $recipient, Constraint $constraint): void
    {
        if ($recipient === $this->participantProvider->getAuthenticatedParticipant()) {
            $this->context->addViolation($constraint->message);
        }
    }
}
