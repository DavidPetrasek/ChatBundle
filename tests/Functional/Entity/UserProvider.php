<?php declare(strict_types=1);

namespace FOS\ChatBundle\Tests\Functional\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    public function loadUserByUsername(string $username): \FOS\ChatBundle\Tests\Functional\Entity\User
    {
        return new User();
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return new User();
    }
}
