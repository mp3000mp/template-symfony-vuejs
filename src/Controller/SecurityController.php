<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ForgottenPasswordType;
use App\Form\Type\LoginType;
use App\Form\Type\ResetPasswordType;
use App\Form\Type\SetPasswordType;
use App\Security\CFSSORedirectSubscriber;
use App\Service\Mailer\MailerService;
use App\Service\SingleSignOn\SSOService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
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
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {

        if($this->getUser() !== null){
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // create form
        $form = $this->createForm(LoginType::class, null, []);

        // set error
        if (null !== $error) {
            $form->addError(new FormError($error->getMessageKey()));
        }

        // set username data
        if (null !== $lastUsername) {
            $form->get('username')->setData($lastUsername);
        }

        // service provider
        $sp = $request->get(SSOService::GET_SP_PARAM);
        if($sp === null && $error !== null){
            $sp = $request->getSession()->get(SSOService::SESSION_SP_URL_KEY);
        }
        if($sp !== null){
            $form->get(SSOService::SESSION_SP_URL_KEY)->setData($sp);
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



}
