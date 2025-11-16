<?php

namespace FOS\ChatBundle\Validator;

use Symfony\Component\Validator\Constraint;

class ReplyAuthorization extends Constraint
{
    public $message = 'fos_chat.reply_not_authorized';

    public function validatedBy() : string
    {
        return 'fos_chat.validator.reply_authorization';
    }

    public function getTargets() : array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
