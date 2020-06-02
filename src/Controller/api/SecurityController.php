<?php declare(strict_types=1);

namespace App\Controller\api;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class SecurityController extends AbstractController
{

    /**
     * @route("/api/test", name="api.test")
     *
     * @return JsonResponse
     */
    public function test()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->json([
            'user_id' => $user->getId(),
            'name' => $user->getUsername(),
        ]);
    }

}
