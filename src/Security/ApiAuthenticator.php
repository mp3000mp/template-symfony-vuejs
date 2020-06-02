<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\ConnectionAuditTrail;
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
 * Class ApiAuthenticator
 *
 * @package App\Security
 */
class ApiAuthenticator extends AbstractGuardAuthenticator
{

    /** @var EntityManagerInterface  */
    private $entityManager;
    /** @var TranslatorInterface  */
    private $translator;

    /**
     * MainAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Does this guard has to been called ?
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->headers->has('authorization');
    }

    /**
     * Get credentials from request
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $auth = $request->headers->get('authorization');
        $arr = explode(' ',$auth);
        $bearer = end($arr);
        $arr = explode('.', $bearer);
        if(count($arr) !== 2){
            return null;
        }else{
            return [
                'api_token' => $arr[0],
                'device_session_token' => $arr[1],
            ];
        }
    }

    /**
     * Try to get user
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
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

        // on check si session ouverte et on récupère le user
        /** @var ConnectionAuditTrail $device */
        $conn = $this->entityManager->getRepository(ConnectionAuditTrail::class)
                        ->findApiSession($credentials['api_token'], $credentials['device_session_token'])
            ;
        return $conn === null ? null : $conn->getUser();
    }

    /**
     * Check credentials
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'message' => 'Authentication Required'
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
