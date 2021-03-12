<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Client\PortalClient;
use App\Service\OTP\OTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class AppController extends AbstractController
{
    /**
     * @route("/", name="home")
     */
    public function home(Request $req): Response
    {
        return $this->json(['status' => 'ok']);
    }
}
