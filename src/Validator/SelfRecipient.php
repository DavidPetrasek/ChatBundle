<?php

namespace FOS\ChatBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SelfRecipient extends Constraint
{
    public $message = 'fos_chat.self_recipient';

    public function validatedBy() : string
    {
        return 'fos_chat.validator.self_recipient';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets() : array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
