<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use App\Service\DeviceSession\DeviceSession;
use App\Service\SingleSignOn\SSOService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SSOController.
 */
class SSOController extends AbstractController
{
    /**
     * @Route("/sso/get-token", name="sso.get_token", methods={"POST"})
     */
    public function getToken(Request $request): Response
    {
        // si pas de service provider, on n'a rien à faire là
        $url = $request->get(SSOService::GET_SP_PARAM);
        if (null === $url) {
            $request->getSession()->getFlashBag()->add('warning', 'Bas request');

            return $this->redirectToRoute('login');
        }

        // si déjà co on redirige avec le token
        if (null !== $this->getUser()) {
            return $this->redirectToRoute(SSOService::ROUTE_SET_TOKEN, [SSOService::GET_SP_PARAM => $url]);
        } else {
            // sinon on redirige vers login avec sp
            return $this->redirectToRoute('login', [SSOService::GET_SP_PARAM => $url]);
        }
    }

    /**
     * @Route("/sso/set-token", name="sso.set_token", methods={"GET"})
     */
    public function setToken(Request $request, SSOService $SSOService): Response
    {
        // si pas de service provider, on n'a rien à faire là
        $url = $request->get(SSOService::GET_SP_PARAM);
        if (null === $url) {
            $request->getSession()->getFlashBag()->add('warning', 'Bas request');

            return $this->redirectToRoute('login');
        }

        // on check application existe
        $subDomain = $SSOService->getSubDomain($url);
        /** @var ApplicationRepository $appRep */
        $appRep = $this->getDoctrine()->getRepository(Application::class);
        $application = $appRep->findOneBy(['sub_domain' => $subDomain]);
        if (null === $subDomain || null === $application || !$appRep->hasUserAccess($this->getUser(), $application)) {
            $request->getSession()->getFlashBag()->add('warning', 'Invalid service provider');

            return $this->redirectToRoute('login');
        } else {
            $request->getSession()->remove(SSOService::SESSION_SP_URL_KEY);

            return $this->render('security.sso_set_token.html.twig', [
                'session_token' => $request->getSession()->get(DeviceSession::SESSION_TOKEN_KEY),
                'url' => $url,
            ]);
        }
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function checkToken(): void
    {
        // todo api platform pour doc
    }
}
