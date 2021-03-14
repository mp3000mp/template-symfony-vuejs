<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class AppController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(MailerService $mailer): Response
    {
        $mailer->sendEmail('test', ['msg' => 'This is a test'], 'Test subject', ['test@mp3000.fr']);

        return $this->json([
            'status' => 'ok',
        ]);
    }
}