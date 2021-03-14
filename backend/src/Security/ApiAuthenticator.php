<?php

declare(strict_types=1);

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ApiAuthenticator.
 */
class ApiAuthenticator extends AbstractGuardAuthenticator
{
    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var TranslatorInterface */
    private $translator;

    /**
     * MainAuthenticator constructor.
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Does this guard has to been called ?
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->headers->has('authorization');
    }

    /**
     * Get credentials from request.
     */
    public function getCredentials(Request $request): ?array
    {
        $auth = $request->headers->get('authorization');
        $arr = explode(' ', $auth);
        $bearer = end($arr);

        return [
            'api_token' => $bearer,
        ];
    }

    /**
     * Try to get user.
     *
     * @param mixed $credentials
     *
     * @return object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (null === $credentials) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            return null;
        }

        // todo
    }

    /**
     * Check credentials.
     *
     * @param mixed $credentials
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param string $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
