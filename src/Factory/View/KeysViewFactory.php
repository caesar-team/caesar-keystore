<?php

declare(strict_types=1);

namespace App\Factory\View;

use App\Entity\Key;
use App\View\KeysView;

class KeysViewFactory
{
    public function createSingle(Key $key): KeysView
    {
        $view = new KeysView();
        $view->setEncryptedPrivateKey($key->getPrivateKey());
        $view->setPublicKey($key->getPublicKey());

        return $view;
    }
}
