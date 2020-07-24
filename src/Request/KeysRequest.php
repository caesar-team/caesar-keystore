<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class KeysRequest
{
    /**
     * @Assert\NotBlank()
     */
    private ?string $encryptedPrivateKey = null;

    /**
     * @Assert\NotBlank()
     */
    private ?string $publicKey = null;

    private string $email;

    private ?string $userId = null;

    public function __construct(string $email, ?string $userId = null)
    {
        $this->email = $email;
        $this->userId = $userId;
    }

    public function getEncryptedPrivateKey(): ?string
    {
        return $this->encryptedPrivateKey;
    }

    public function setEncryptedPrivateKey(?string $encryptedPrivateKey): void
    {
        $this->encryptedPrivateKey = $encryptedPrivateKey;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(?string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setUserId(?string $userId): void
    {
        $this->userId = $userId;
    }
}
