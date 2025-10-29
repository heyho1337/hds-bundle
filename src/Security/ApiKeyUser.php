<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiKeyUser implements UserInterface
{
    public function getRoles(): array
    {
        return ['ROLE_API'];  // Role assigned to API key authenticated users
    }

    public function getPassword()
    {
        return null; // No password required for API key user
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return 'api_user';
    }

    public function eraseCredentials(): void
    {
        // No sensitive info to erase
    }

    public function getUserIdentifier(): string
    {
        return 'api_user';
    }
}
