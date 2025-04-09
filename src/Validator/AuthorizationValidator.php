<?php

namespace FOS\ChatBundle\Validator;

use FOS\ChatBundle\Security\AuthorizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AuthorizationValidator extends ConstraintValidator
{
    public function __construct(private readonly AuthorizerInterface $authorizer)
    {
    }

    /**
     * Indicates whether the constraint is valid.
     */
    public function validate(object $recipient, Constraint $constraint): void
    {
        if ($recipient && !$this->authorizer->canMessageParticipant($recipient)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
