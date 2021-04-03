<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Helper\Request\JsonRequestHelper;
use App\Helper\Response\JsonResponseHelper;
use App\Service\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $em;

    private JsonRequestHelper $requestHelper;
    private JsonResponseHelper $responseHelper;

    public function __construct(EntityManagerInterface $em, JsonRequestHelper $requestHelper, JsonResponseHelper $responseHelper)
    {
        $this->em = $em;
        $this->responseHelper = $responseHelper;
        $this->requestHelper = $requestHelper;
    }

    /**
     * @Route("/api/me", name="users.me", methods={"GET"})
     */
    public function me(): Response
    {
        return $this->responseHelper->createResponse($this->getUser(), ['me'], 200);
    }

    /**
     * @Route("/api/users", name="users.index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->responseHelper->createResponse($users, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}", name="users.show", methods={"GET"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(User $user): Response
    {
        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users", name="users.create", methods={"PUT"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function create(Request $request, MailerService $mailer): Response
    {
        $user = $this->requestHelper->handleRequest($request->getContent(), 'user_all',User::class);

        if($user->getIsEnabled()){
            // set reset token
            $user->setResetPasswordAt(new \DateTime());
            $user->generateResetPasswordToken();
        }

        $this->em->persist($user);
        $this->em->flush();

        if($user->getIsEnabled()) {
            // send mail
            $mailer->sendEmail('welcome', [
                'reset_url' => $this->getParameter('FRONT_URL') . '/password/init/' . $user->getResetPasswordToken(),
            ], 'Welcome to mp3000', [$user->getEmail()]);
        }

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}", name="users.update", methods={"POST"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function update(Request $request, User $user, MailerService $mailer): Response
    {
        $wasEnabled = $user->getIsEnabled();
        $user = $this->requestHelper->handleRequest($request->getContent(), 'user_all', User::class, $user);

        if (!$user->getIsEnabled() && $user->getId() === $this->getUser()->getId()) {
            return $this->json([
                'message' => 'You cannot disable yourself.',
            ], 400);
        }

        if(!$wasEnabled && $user->getIsEnabled()){
            // set reset token
            $user->setResetPasswordAt(new \DateTime());
            $user->generateResetPasswordToken();
        }

        $this->em->persist($user);
        $this->em->flush();

        if(!$wasEnabled && $user->getIsEnabled()){
            // send mail
            $mailer->sendEmail('welcome', [
                'reset_url' => $this->getParameter('FRONT_URL') . '/password/init/' . $user->getResetPasswordToken(),
            ], 'Welcome to mp3000', [$user->getEmail()]);
        }

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}/enable", name="users.enable", methods={"POST"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function enable(User $user, MailerService $mailer): Response
    {
        if ($user->getIsEnabled()) {
            return $this->json([
                'message' => 'This user is already enabled.',
            ], 400);
        }

        // set reset token
        $user->setResetPasswordAt(new \DateTime());
        $user->generateResetPasswordToken();
        $user->setIsEnabled(true);

        // persit
        $this->em->persist($user);
        $this->em->flush();

        // send mail
        $mailer->sendEmail('welcome', [
            'reset_url' => $this->getParameter('FRONT_URL').'/password/init/'.$user->getResetPasswordToken(),
        ], 'Welcome to mp3000', [$user->getEmail()]);

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}/disable", name="users.disable", methods={"POST"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function disable(User $user): Response
    {
        if (!$user->getIsEnabled()) {
            return $this->json([
                'message' => 'This user is already disabled.',
            ], 400);
        }
        if ($user->getId() === $this->getUser()->getId()) {
            return $this->json([
                'message' => 'You cannot disable yourself.',
            ], 400);
        }

        $user->setIsEnabled(false);

        // persist
        $this->em->persist($user);
        $this->em->flush();

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/api/users/{id}", name="users.remove", methods={"DELETE"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function remove(User $user): Response
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
