<?php

declare(strict_types=1);

namespace App\Model;

use App\View\ExternalUserView;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    private const ROLE_DEFAULT = 'ROLE_USER';

    private string $id;

    private string $email;

    private array $roles = [];

    private ?string $name;

    private ?string $avatar;

    public static function createFromExternalUser(ExternalUserView $view): self
    {
        $user = new self();
        $user->id = $view->getId();
        $user->email = $view->getEmail();
        $user->roles = $view->getRoles();
        $user->name = $view->getName();
        $user->avatar = $view->getAvatar();

        return $user;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        $this->roles[] = self::ROLE_DEFAULT;

        return array_unique($this->roles);
    }

    public function getUsername()
    {
        $this->email;
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}
