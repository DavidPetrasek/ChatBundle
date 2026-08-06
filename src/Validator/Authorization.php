<?php

declare(strict_types=1);

namespace FOS\ChatBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Authorization extends Constraint
{
    public $message = 'fos_chat.not_authorized';

    public function validatedBy() : string
    {
        return 'fos_chat.validator.authorization';
    }

    public function getTargets() : array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
