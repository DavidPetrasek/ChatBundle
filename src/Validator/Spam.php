<?php

namespace FOS\ChatBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Spam extends Constraint
{
    public $message = 'fos_user.body.spam';

    public function validatedBy() : string
    {
        return 'fos_chat.validator.spam';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets() : array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
