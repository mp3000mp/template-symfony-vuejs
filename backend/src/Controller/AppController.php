<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppController extends AbstractController
{
    /**
     * @Route("/api/info", name="info", methods={"GET"})
     */
    public function info(ParameterBagInterface $parameterBag): Response
    {
        return $this->json([
            'version' => $parameterBag->get('APP_VERSION'),
        ]);
    }

    /**
     * @Route("/api/me", name="users.me", methods={"GET"})
     */
    public function me(): Response
    {
        return $this->responseHelper->createResponse($this->getUser(), ['me'], 200);
    }
}
