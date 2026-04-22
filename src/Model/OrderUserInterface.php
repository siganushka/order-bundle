<?php

declare(strict_types=1);

namespace Siganushka\OrderBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * To decouple the relationship between resources and users, you can use `resolve_target_entities` to make it work.
 */
interface OrderUserInterface extends UserInterface
{
}
