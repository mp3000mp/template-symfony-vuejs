<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Service\OTP\OTPService;
use App\Service\SharedSession\SharedSession;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Logout if session has expired
 * Class ExpiredSessionSubscriber
 *
 * @package App\EventSubscriber
 */
class ExpiredSessionSubscriber implements EventSubscriberInterface
{
    private const SESSION_EXPIRATION_SECONDS = 300;

    /** @var RouterInterface  */
    private $router;
    /** @var TokenStorageInterface  */
    private $tokenStorage;
    /** @var SharedSession  */
    private $sharedSession;

    /**
     * ExpiredSessionSubscriber constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param RouterInterface $router
     * @param SharedSession $sharedSession
     */
    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, SharedSession $sharedSession)
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->sharedSession = $sharedSession;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -30]],
        ];
    }

    /**
     * @param RequestEvent $event
     *
     * @throws \Exception
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // if connected
        $currentToken = $this->tokenStorage->getToken();

        if ($currentToken instanceof PostAuthenticationGuardToken) {
            /** @var User $user */
            $user = $currentToken->getUser();

            // if authenticated
            if (null !== $user->getTwoFactorSecret() && in_array(OTPService::ROLE_TWO_FACTOR_SUCCEED, $currentToken->getRoleNames(), true)) {

                // on compare shared token en local et en redis
                /** @var Session $session */
                $session = $event->getRequest()->getSession();

                // si pas encore de shared session, on la créé
                if (!$session->has('shared_session')) {
                    $this->sharedSession->create($user);
                }

                // si session redis existe pas => timeout ou que session en base ne correspond pas (secours si socket server down)
                if (!$this->sharedSession->exists($session->get('shared_session'))) {
                    // si anomalie on kill la session
                    $this->sharedSession->destroy($user);
                    $this->tokenStorage->setToken(null);
                    $session->getFlashBag()->add('info', 'security.session_error');
                    $response = new RedirectResponse($this->router->generate('login'));
                    $event->setResponse($response);
                    return;
                }
            }
        }
    }
}
