<?php

namespace FOS\ChatBundle\Tests\Functional\Form;

use FOS\ChatBundle\Tests\Functional\Entity\User;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class UserToUsernameTransformer implements DataTransformerInterface
{
    public function transform($value): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof User) {
            throw new \RuntimeException();
        }

        return $value->getUsername();
    }

    /**
     * Transforms a username string into a UserInterface instance.
     * @throws UnexpectedTypeException if the given value is not a string
     */
    public function reverseTransform(mixed $value): \FOS\ChatBundle\Tests\Functional\Entity\User
    {
        return new User();
    }
}
