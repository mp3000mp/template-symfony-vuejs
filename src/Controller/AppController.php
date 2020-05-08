<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Service\OTP\OTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class AppController extends AbstractController
{
    /**
     * @route("/", name="home")
     *
     * @return Response
     */
    public function home(): Response
    {
        return $this->render('app/index.html.twig', [
            'msg' => 'Hello world !',
        ]);
    }

    /**
     * @route("/account", name="account")
     *
     * @param Request $request
     * @param OTPService $OTPService
     *
     * @return Response
     */
    public function account(Request $request, OTPService $OTPService): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if($request->get('newsecret') !== null || $user->getTwoFactorSecret() === null){
            $otp = $OTPService->generateNewOtp($user);
        }else{
            $otp = $OTPService->getUserOTP($user);
        }

        return $this->render('app/account.html.twig', [
            'user' => $this->getUser(),
            'otpUrl' => $otp->getProvisioningUri(),
            'showqr' => $request->get('showqr'),
        ]);
    }

    /**
     * @route("/ping", name="ping")
     *
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return $this->json(['msg' => 'pong']);
    }

    /**
     * @route("/test", name="test")
     *
     * @return RedirectResponse
     */
    public function test(): RedirectResponse
    {
        return $this->redirectToRoute('test.test');
    }

    /**
     * @route("/test/test", name="test.test")
     *
     * @return Response
     */
    public function testTest(): Response
    {
        return $this->render('app/test.html.twig', [
        ]);
    }

    /**
     * @route("/lang/{lang}/select", name="lang", requirements={"lang"="[a-z]{2}"})
     *
     * @param string $lang
     * @param Request $request
     *
     * @return Response
     */
    public function lang(string $lang, Request $request): Response
    {
        $referer = $request->headers->get('referer');
        $request->getSession()->set('_locale', $lang);

        return new RedirectResponse($referer, 307);
    }

}
