<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/users")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="users.index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->responseHelper->createResponse($users, ['admin'], 200);
    }

    /**
     * @Route("/{id}", name="users.show", methods={"GET"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function show(User $user): Response
    {
        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("", name="users.create", methods={"POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function create(Request $request, MailerService $mailer): Response
    {
        $user = $this->requestHelper->handleRequest($request->getContent(), 'user_all', User::class);

        if ($user->getIsEnabled()) {
            // set reset token
            $user->setResetPasswordAt(new \DateTime());
            $user->generateResetPasswordToken();
        }

        $this->em->persist($user);
        $this->em->flush();

        if ($user->getIsEnabled()) {
            // send mail
            $mailer->sendEmail('welcome', [
                'reset_url' => $this->getParameter('FRONT_URL').'/password/init/'.$user->getResetPasswordToken(),
            ], 'Welcome to mp3000', [$user->getEmail()]);
        }

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/{id}", name="users.update", methods={"PUT"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function update(Request $request, User $user, MailerService $mailer): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $wasEnabled = $user->getIsEnabled();
        $user = $this->requestHelper->handleRequest($request->getContent(), 'user_all', User::class, $user);

        if (!$user->getIsEnabled() && $user->getId() === $currentUser->getId()) {
            return $this->json([
                'message' => 'You cannot disable yourself.',
            ], 400);
        }

        if (!$wasEnabled && $user->getIsEnabled()) {
            // set reset token
            $user->setResetPasswordAt(new \DateTime());
            $user->generateResetPasswordToken();
        }

        $this->em->persist($user);
        $this->em->flush();

        if (!$wasEnabled && $user->getIsEnabled()) {
            // send mail
            $mailer->sendEmail('welcome', [
                'reset_url' => $this->getParameter('FRONT_URL').'/password/init/'.$user->getResetPasswordToken(),
            ], 'Welcome to mp3000', [$user->getEmail()]);
        }

        return $this->responseHelper->createResponse($user, ['admin'], 200);
    }

    /**
     * @Route("/{id}/enable", name="users.enable", methods={"PUT"}, requirements={"id"="\d+"})
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
     * @Route("/{id}/disable", name="users.disable", methods={"PUT"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function disable(User $user): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if (!$user->getIsEnabled()) {
            return $this->json([
                'message' => 'This user is already disabled.',
            ], 400);
        }
        if ($user->getId() === $currentUser->getId()) {
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
     * @Route("/{id}", name="users.remove", methods={"DELETE"}, requirements={"id"="\d+"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function remove(User $user): Response
    {
        if ($user->getIsEnabled()) {
            return $this->json([
                'message' => 'You cannot delete an enabled user.',
            ], 400);
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
