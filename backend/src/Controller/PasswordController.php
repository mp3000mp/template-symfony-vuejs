<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\Mailer\MailerService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordController extends AbstractController
{

    /**
     * Send reset password email
     *
     * @Route("/api/password/forgotten", name="password_forgotten", methods={"POST"})
     */
    public function forgottenPassword(Request $request, MailerService $mailer, LoggerInterface $logger): Response
    {
        $json = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $em->getRepository(User::class)
            ->findOneBy(['email' => $json['email'] ?? null])
        ;

        if($user !== null){
            $logger->debug(sprintf('Forgotten password requested by %s', $user->getEmail()));
            // set reset token
            $user->setResetPasswordAt(new \DateTime());
            $user->setResetPasswordToken(md5(random_bytes(64)));

            // send mail
            $mailer->sendEmail('forgotten_password', [
                'reset_url'   => $this->getParameter('FRONT_URL').'/password/reset/'.$user->getResetPasswordToken(),
            ], 'Forgotten password', [$user->getEmail()]);

            // persist
            $em->persist($user);
            $em->flush();
        }

        return $this->json([
            'message' => 'If this account exists, an email has been sent.'
        ]);
    }

    /**
     * Test if reset token ok
     *
     * @Route("/api/password/reset/{token}", name="password_reset_check", methods={"GET"}, requirements={"token"="\w+"})
     */
    public function resetPasswordUser(Request $request, string $token): Response
    {
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $em->getRepository(User::class)
            ->findOneBy(['reset_password_token' => $token])
        ;

        if($user === null) {
            return $this->json([
                'message' => 'This token has expired.'
            ], 404);
        }

        return $this->json([
            'message' => 'ok',
        ]);
    }

    /**
     * Reset password
     *
     * @Route("/api/password/reset", name="password_reset", methods={"POST"})
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $json = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();

        // get users
        $user = $em->getRepository(User::class)
            ->findOneBy(['reset_password_token' => $json['token'] ?? null])
        ;

        if($user === null) {
            return $this->json([
                'message' => 'This token has expired.'
            ], 404);
        }

        // todo password constraints

        // test password validity
        if(strlen(($json['password'] ?? '' )) < 9) {
            return $this->json([
                'message' => 'This password is not strong enough.'
            ], 400);
        }

        // change password
        $user->setPasswordUpdatedAt(new \DateTime());
        $user->setResetPasswordAt(null);
        $user->setResetPasswordToken(null);
        $user->setPassword($encoder->encodePassword($user, $json['password']));

        // persist
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'The password has been reset successfully',
        ]);
    }
}
