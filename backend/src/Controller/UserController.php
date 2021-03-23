<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Helper\Response\JsonResponseHelper;
use App\Service\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var JsonResponseHelper
     */
    private JsonResponseHelper $responseHelper;

    public function __construct(EntityManagerInterface $em, JsonResponseHelper $responseHelper)
    {
        $this->em = $em;
        $this->responseHelper = $responseHelper;
    }

    /**
     * @Route("/api/users", name="users.index", methods={"GET"})
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->responseHelper->createResponse($users, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}", name="users.show", methods={"GET"}, requirements={"page"="\d+"})
     */
    public function show(User $user): Response
    {
        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users", name="users.create", methods={"PUT"}, requirements={"page"="\d+"})
     */
    public function create(Request $request, SerializerInterface $serializer): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $this->em->persist($user);
        $this->em->flush();

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}", name="users.update", methods={"POST"}, requirements={"page"="\d+"})
     */
    public function update(User $user): Response
    {
        // todo update $user

        $this->em->persist($user);
        $this->em->flush();

        return $this->responseHelper->createResponse($user, ['admin'], 200);
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
