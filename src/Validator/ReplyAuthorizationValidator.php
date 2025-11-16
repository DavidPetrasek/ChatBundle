<?php

namespace FOS\ChatBundle\Validator;

use FOS\ChatBundle\Security\AuthorizerInterface;
use FOS\ChatBundle\Security\ParticipantProviderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ReplyAuthorizationValidator extends ConstraintValidator
{
    public function __construct
    (
        private readonly AuthorizerInterface $authorizer, 
        private readonly ParticipantProviderInterface $participantProvider
    )
    {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        $sender = $this->participantProvider->getAuthenticatedParticipant();
        $recipients = $value->getThread()->getOtherParticipants($sender);

        foreach ($recipients as $recipient) 
        {
            if (!$this->authorizer->canMessageParticipant($recipient)) 
            {
                $this->context->addViolation($constraint->message);

                return;
            }
        }
    }
}
