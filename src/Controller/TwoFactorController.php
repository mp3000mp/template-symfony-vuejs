<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\TwoFactorType;
use App\Service\OTP\OTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class TwoFactorController.
 */
class TwoFactorController extends AbstractController
{

    /**
     * @Route("/two-factor", name="two_factor")
     *
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     *
     * @param OTPService $OTPService
     *
     * @return Response
     */
    public function twoFactor(Request $request, TokenStorageInterface $tokenStorage, OTPService $OTPService): Response
    {
        // if already auth => home
        /** @var PostAuthenticationGuardToken $currentToken */
        $currentToken = $tokenStorage->getToken();
        /** @var User $user */
        $user = $this->getUser();
        if(in_array(OTPService::ROLE_TWO_FACTOR_SUCCEED, $currentToken->getRoleNames(),true)){
            return $this->redirectToRoute('home');
        }

        $otp = $OTPService->getUserOTP($user);

        $form = $this->createForm(TwoFactorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();
            if(!$otp->verify($code)){
                $form->get('code')->addError(new FormError('security.connexion.err.two_factor_bad_code'));
            }else{
                $OTPService->addTwoFactorRole($tokenStorage, $request->getSession());
                return $this->redirectToRoute('home'); // todo target
            }
        }

        return $this->render('security/double_factor.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @route("/two-factor/enable", name="two_factor.enable")
     *
     * @param Request $request
     * @param OTPService $OTPService
     *
     * @return Response
     */
    public function enable(Request $request, OTPService $OTPService): Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();

        $secret = $OTPService->generateSecret();
        $user->setTwoFactorSecret($secret);

        $em->persist($user);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'security.two_factor.msg.enabled');

        return $this->redirectToRoute('account', ['showqr' => true]);
    }

    /**
     * @route("/two-factor/disable", name="two_factor.disable")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function disable(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $this->getUser();
        $user->setTwoFactorSecret(null);
        $em->persist($user);
        $em->flush();
        $request->getSession()->getFlashBag()->add('success', 'security.two_factor.msg.disabled');
        return $this->redirectToRoute('account');
    }



}
