<?php declare(strict_types=1);

namespace App\Controller\api;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * Class UserController.
 */
class UserController
{

    /**
     * @Get(
     *     path = "/api/user/{id}",
     *     name = "testapi",
     *     requirements = {"id"="\d+"}
     * )
     * @View(
     *     serializerGroups = {"g1"}
     * )
     *
     * @param User $user
     */
    public function show(User $user)
    {
        return $user;
    }

}
