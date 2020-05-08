<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ForgottenPasswordType;
use App\Form\Type\LoginType;
use App\Form\Type\ResetPasswordType;
use App\Form\Type\SetPasswordType;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AppController.
 */
class SecurityController extends AbstractController
{

    /**
     * @Route("/login", name="login", methods={"GET"})
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // create form
        $form = $this->createForm(LoginType::class, null, [
            //'captcha' => $error !== null,
        ]);

        // set error
        if($error !== null){
            $form->addError(new FormError($error->getMessageKey()));
        }

        // set username data
        if($lastUsername !== null){
            $form->get('username')->setData($lastUsername);
        }

        // view
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logincheck", name="login.check", methods={"POST"})
     */
    public function loginCheck(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('login check in security.yaml undefined');
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


    /**
     * @Route("/reset-password", name="reset_password")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {

        // create form
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // get user
            /** @var User $user */
            $user = $this->getUser();

            // check password
            if($encoder->isPasswordValid($user, $form->get('password_current')->getData())){
                // change password
                $newPassword = $form->get('password_new')->getData();
                $user->setPasswordUpdatedAt(new \DateTime());
                $user->setResetPasswordAt(null);
                $user->setResetPasswordToken(null);
                $user->setPassword($encoder->encodePassword($user, $newPassword));

                // persist
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                // redirect
                $this->addFlash('success', 'security.reset_password_success');
                return $this->redirectToRoute('home');
            }else{
                $form->get('password_current')->addError(new FormError('security.connexion.err.bad_password'));
            }
        }

        // view
        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/forgotten-password", name="forgotten_password")
     *
     * @param Request $request
     * @param MailerService $mailerService
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function forgottenPassword(Request $request, MailerService $mailerService)
    {

        // create form
        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // get user
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)
                ->findOneBy(['email' => $form->get('email')->getData()]);

            if($user !== null){
                // set reset token
                $user->setResetPasswordAt(new \DateTime());
                $user->setResetPasswordToken(md5(random_bytes(64)));

                // send mail
                $mailerService->sendEmail('forgotten_password', [
                    'token' => $user->getResetPasswordToken(),
                ], 'security.forgotten_password', [], [$user->getEmail()]);

                // persist
                $em->persist($user);
                $em->flush();
            }

            // redirect login
            $request->getSession()->getFlashBag()->add('info', 'security.forgotten_password.msg.success');
            return $this->redirectToRoute('login');
        }

        // view
        return $this->render('security/forgotten_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/set-password/{token}", name="set_password")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function setPassword(Request $request, UserPasswordEncoderInterface $encoder, string $token)
    {
        // get user from token
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->findOneBy(['reset_password_token' => $token]);

        // if not user, bad token
        if($user === null){
            $request->getSession()->getFlashBag()->add('warning', 'security.set_password.bad_token');
            return $this->redirectToRoute('login');
        }

        // create form
        $form = $this->createForm(SetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // change password
            $newPassword = $form->get('password_new')->getData();
            $user->setPasswordUpdatedAt(new \DateTime());
            $user->setResetPasswordAt(null);
            $user->setResetPasswordToken(null);
            $user->setPassword($encoder->encodePassword($user, $newPassword));

            // persist
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // redirect
            $this->addFlash('success', 'security.set_password_success');
            return $this->redirectToRoute('home');
        }

        // view
        return $this->render('security/set_password.html.twig', [
            'form' => $form->createView(),
        ]);

    }

}
