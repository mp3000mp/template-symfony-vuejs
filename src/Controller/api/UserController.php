<?php

declare(strict_types=1);

namespace App\Controller\api;

use App\Entity\User;

/**
 * Class UserController.
 */
class UserController
{
    public function show(User $user): User
    {
        return $user;
    }
}
