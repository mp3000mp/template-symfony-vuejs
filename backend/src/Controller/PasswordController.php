<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    /**
     * Send reset password email.
     *
     * @Route("/api/password/forgotten", name="password_forgotten_send", methods={"POST"})
     */
    public function forgottenPasswordSend(Request $request, MailerService $mailer, LoggerInterface $logger): Response
    {
        $json = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => $json['email'] ?? null])
        ;

        if (null !== $user && $user->getIsEnabled()) {
            $logger->debug(sprintf('Forgotten password requested by %s', $user->getEmail()));
            // set reset token
            $user->setResetPasswordAt(new \DateTime());
            $user->generateResetPasswordToken();

            // persist
            $em->persist($user);
            $em->flush();

            // send mail
            $mailer->sendEmail('forgotten_password', [
                'reset_url' => $this->getParameter('FRONT_URL').'/password/forgotten/'.$user->getResetPasswordToken(),
            ], 'Forgotten password', [$user->getEmail()]);
        }

        return $this->json([
            'message' => sprintf('If this account exists, an email has been sent to %s', $json['email']),
        ]);
    }

    /**
     * Test if reset token ok.
     *
     * @Route("/api/password/init/{token}", name="password_init_check", methods={"GET"},  requirements={"token"="\w+"})
     * @Route("/api/password/forgotten/{token}", name="password_forgotten_check", methods={"GET"}, requirements={"token"="\w+"})
     */
    public function forgottenPasswordCheck(string $token): Response
    {
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $em->getRepository(User::class)
            ->findOneBy(['reset_password_token' => $token])
        ;

        if (null === $user || !$user->getIsEnabled()) {
            return $this->json([
                'message' => 'This token has expired.',
            ], 404);
        }

        return $this->json([
            'message' => 'Token is valid.',
        ]);
    }

    /**
     * Reset password.
     *
     * @Route("/api/password/init/{token}", name="password_init_reset", methods={"POST"},  requirements={"token"="\w+"})
     * @Route("/api/password/forgotten/{token}", name="password_forgotten_reset", methods={"POST"},  requirements={"token"="\w+"})
     */
    public function forgottenPasswordReset(Request $request, string $token, UserPasswordHasherInterface $hasher): Response
    {
        $json = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $em->getRepository(User::class)
            ->findOneBy(['reset_password_token' => $token])
        ;

        if (null === $user) {
            return $this->json([
                'message' => 'This token has expired.',
            ], 404);
        }

        // check newPassword confirmation
        if ($json['password'] !== $json['passwordConfirm']) {
            return $this->json([
                'message' => 'Password confirmation is different.',
            ], 400);
        }

        // todo password constraints

        // test password validity
        if (strlen(($json['password'] ?? '')) < 9) {
            return $this->json([
                'message' => 'This password is not strong enough.',
            ], 400);
        }

        // change password
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setResetPasswordAt(null);
        $user->setResetPasswordToken(null);
        $user->setPassword($hasher->hashPassword($user, $json['password']));

        // persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'The password has been reset successfully.',
        ]);
    }

    /**
     * Reset password.
     *
     * @Route("/api/password/reset", name="password_reset", methods={"POST"})
     */
    public function resetPassword(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $json = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $this->getUser();

        // check currentPassword
        if (!$hasher->isPasswordValid($user, $json['currentPassword'])) {
            return $this->json([
                'message' => 'Authentication failed.',
            ], 401);
        }

        // check newPassword confirmation
        if ($json['newPassword'] !== $json['newPassword2']) {
            return $this->json([
                'message' => 'Password confirmation is different.',
            ], 400);
        }

        // todo password constraints

        // test password validity
        if (strlen(($json['newPassword'] ?? '')) < 9) {
            return $this->json([
                'message' => 'This password is not strong enough.',
            ], 400);
        }

        // change password
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setResetPasswordAt(null);
        $user->setResetPasswordToken(null);
        $user->setPassword($hasher->hashPassword($user, $json['newPassword']));

        // persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'The password has been reset successfully',
        ]);
    }
}
