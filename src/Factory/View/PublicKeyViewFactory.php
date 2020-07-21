<?php

declare(strict_types=1);

namespace App\Factory\View;

use App\Entity\Key;
use App\View\PublicKeyView;

class PublicKeyViewFactory
{
    public function createSingle(Key $key): PublicKeyView
    {
        $view = new PublicKeyView();
        $view->setUserId($key->getUserId());
        $view->setPublicKey($key->getPublicKey());
        $view->setEmail($key->getEmail());

        return $view;
    }

    /**
     * @param Key[] $users
     *
     * @return PublicKeyView[]
     */
    public function createCollection(array $users): array
    {
        return array_map([$this, 'createSingle'], $users);
    }
}
