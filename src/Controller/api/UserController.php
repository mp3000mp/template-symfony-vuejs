<?php declare(strict_types=1);

namespace App\Controller\api;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController
{

    /**
     *
     * @param User $user
     */
    public function show(User $user)
    {
        return $user;
    }

}
