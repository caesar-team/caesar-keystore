<?php

declare(strict_types=1);

namespace App\View;

class ExternalUserView
{
    private string $id;

    private string $email;

    private ?string $name;

    private ?string $avatar;

    private array $roles = [];

    private array $teamIds = [];

    public static function createFromResponse(array $response): self
    {
        if (!isset($response['id'])
            || !isset($response['email'])
            || !isset($response['roles'])
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid response, required fields: `id`, `email` and `roles`, but contains: %s',
                implode('`, `', array_keys($response)))
            );
        }

        if (!is_array($response['roles'])) {
            $response['roles'] = [$response['roles']];
        }

        $view = new self();
        $view->id = $response['id'];
        $view->email = $response['email'];
        $view->roles = $response['roles'];
        $view->name = $response['name'] ?? null;
        $view->avatar = $response['avatar'] ?? null;
        $view->teamIds = $response['teamIds'] ?? [];

        return $view;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getTeamIds(): array
    {
        return $this->teamIds;
    }

    public function setTeamIds(array $teamIds): void
    {
        $this->teamIds = $teamIds;
    }
}
