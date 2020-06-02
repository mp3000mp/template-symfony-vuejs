<?php declare(strict_types=1);

namespace App\Controller\api;

use App\Entity\User;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController.
 */
class UserController extends AbstractFOSRestController
{

    /**
     * @route("/api/user/{id}", name="api.test", requirements={"id"="\d+"})
     *
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(User $user)
    {
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

}
