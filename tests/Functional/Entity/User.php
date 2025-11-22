<?php

namespace FOS\ChatBundle\Tests\Functional\Entity;

use FOS\ChatBundle\Model\ParticipantInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements ParticipantInterface, UserInterface
{
    public function getUsername(): string
    {
        return 'guilhem';
    }

    public function getPassword(): string
    {
        return 'pass';
    }

    public function getSalt()
    {
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return 'user';
    }

    public function getId(): ?int
    {
        return 1;
    }
}
