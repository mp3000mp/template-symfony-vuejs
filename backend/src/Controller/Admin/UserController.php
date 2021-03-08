<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class UserController extends AbstractController
{
    /**
     * @route("/admin/users", name="admin.users.index")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)
            ->findAll();

        dump($users);

        return $this->render('admin/users/index.html.twig', [
            ]);
    }
}
