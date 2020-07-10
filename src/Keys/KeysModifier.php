<?php

declare(strict_types=1);

namespace App\Keys;

use App\Entity\Key;
use App\Repository\KeyRepositoryInterface;
use App\Request\KeysRequest;

class KeysModifier
{
    private KeyRepositoryInterface $repository;

    public function __construct(KeyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createByRequest(KeysRequest $request): Key
    {
        $key = $this->repository->getKeyByEmail($request->getEmail());
        if (null !== $key) {
            throw new \InvalidArgumentException();
        }

        $key = new Key();
        $key->setPublicKey($request->getPublicKey());
        $key->setPrivateKey($request->getEncryptedPrivateKey());
        $key->setEmail($request->getEmail());
        $key->setUserId($request->getUserId());

        $this->repository->save($key);

        return $key;
    }

    public function createOrUpdateByRequest(KeysRequest $request): Key
    {
        $key = $this->repository->getKeyByEmail($request->getEmail());
        if (null === $key) {
            $key = new Key();
            $key->setEmail($request->getEmail());
            $key->setUserId($request->getUserId());
        }

        $key->setPublicKey($request->getPublicKey());
        $key->setPrivateKey($request->getEncryptedPrivateKey());

        $this->repository->save($key);

        return $key;
    }
}
