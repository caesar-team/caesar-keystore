<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;

/**
 * @method User getUser()
 */
abstract class AbstractController extends BaseAbstractController
{
}
