<?php

namespace FOS\ChatBundle\Validator;

use FOS\ChatBundle\SpamDetection\SpamDetectorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SpamValidator extends ConstraintValidator
{
    public function __construct(private SpamDetectorInterface $spamDetector)
    {
    }

    /**
     * Indicates whether the constraint is valid.
     */
    public function validate(object $value, Constraint $constraint): void
    {
        if ($this->spamDetector->isSpam($value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
