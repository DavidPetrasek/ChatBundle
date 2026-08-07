<?php

namespace FOS\ChatBundle\Validator;

use FOS\ChatBundle\Model\SpamStatus;
use FOS\ChatBundle\Service\SpamDetection\SpamDetectorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SpamValidator extends ConstraintValidator
{
    public function __construct
    (
        private readonly SpamDetectorInterface $spamDetector
    )
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($this->spamDetector->check($value) !== SpamStatus::HAM) 
        {
            $this->context->addViolation($constraint->message);
        }
    }
}
