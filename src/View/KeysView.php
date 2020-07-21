<?php

declare(strict_types=1);

namespace App\View;

use Swagger\Annotations as SWG;

class KeysView
{
    /**
     * @SWG\Property(type="string", example="asdfasdra34w56")
     */
    private ?string $encryptedPrivateKey = null;

    /**
     * @SWG\Property(type="string", example="asdfassdaaw46t4wesdra34w56")
     */
    private ?string $publicKey = null;

    public function __construct()
    {
        $this->encryptedPrivateKey = null;
        $this->publicKey = null;
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
}
