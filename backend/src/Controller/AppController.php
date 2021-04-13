<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Mp3000mp\RedisClient\RedisClient;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(MailerService $mailer, RedisClient $redis, LoggerInterface $logger): Response
    {
        $this->getDoctrine()->getRepository(User::class)->doError();
        $logger->debug('Home called');

        // test mail
        $mailer->sendEmail('test', ['msg' => 'This is a test'], 'Test subject', ['test@mp3000.fr']);

        // test sql
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        // test redis
        $redis->set('test', ['status' => 'ok'], 60);
        $redisStr = $redis->get('test');

        return $this->json([
            'email' => 'ok',
            'sql' => count($users) > 0 ? 'ok' : 'nok',
            'redis' => $redisStr['status'],
        ]);
    }
}
