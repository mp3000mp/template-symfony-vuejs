<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(MailerService $mailer, LoggerInterface $logger): Response
    {
        $logger->debug('Home called');

        // test mail
        $mailer->sendEmail('test', ['msg' => 'This is a test'], 'Test subject', ['test@mp3000.fr']);

        // test sql
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->json([
            'email' => 'ok',
            'sql' => count($users) > 0 ? 'ok' : 'nok',
        ]);
    }
}
