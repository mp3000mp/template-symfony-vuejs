<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/api/users", name="users.index", methods={"GET"})
     */
    public function index(SerializerInterface $serializer, LoggerInterface $logger): Response
    {
        $logger->info('test info');
        $logger->error('test error');

        $users = $this->em->getRepository(User::class)->findAll();

        return new Response(
            $serializer->serialize($users, 'json', ['groups' => 'admin']),
            200,
            ['content-type' => 'application/json'],
        );
    }

    /**
     * @Route("/api/users/{id}", name="users.show", methods={"GET"}, requirements={"page"="\d+"})
     */
    public function show(User $user, SerializerInterface $serializer): Response
    {
        return new Response(
            $serializer->serialize($user, 'json', ['groups' => 'admin']),
            200,
            ['content-type' => 'application/json'],
        );
    }

    /**
     * @Route("/api/users", name="users.create", methods={"PUT"}, requirements={"page"="\d+"})
     */
    public function create(Request $request, SerializerInterface $serializer): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $this->em->persist($user);
        $this->em->flush();

        return $this->json(null, 201);
    }

    /**
     * @Route("/api/users/{id}", name="users.update", methods={"POST"}, requirements={"page"="\d+"})
     */
    public function update(User $user): Response
    {
        $this->em->persist($user);
        $this->em->flush();

        return $this->json(null, 204);
    }

    /**
     * @Route("/api/users/{id}", name="users.remove", methods={"DELETE"}, requirements={"page"="\d+"})
     */
    public function remove(User $user): Response
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
