<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Service\DeviceSession\DeviceSession;
use App\Service\OTP\OTPService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Init device session
 * Class DeviceSessionSubscriber
 *
 * @package App\EventSubscriber
 */
class DeviceSessionSubscriber implements EventSubscriberInterface
{
    /** @var string  */
    private const FIREWALL_NAME = 'main';

    /** @var TokenStorageInterface  */
    private $tokenStorage;
    /** @var DeviceSession  */
    private $deviceSession;

    /**
     * ExpiredSessionSubscriber constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     * @param DeviceSession $deviceSession
     */
    public function __construct(TokenStorageInterface $tokenStorage, DeviceSession $deviceSession)
    {
        $this->tokenStorage = $tokenStorage;
        $this->deviceSession = $deviceSession;
    }

    /**
     * @return array[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', -10]],
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
            /** @var Session $session */
            $session = $event->getRequest()->getSession();

            // if authenticated and not already device session
            if (self::FIREWALL_NAME === $currentToken->getProviderKey()
                && !$session->has('device_session_token')
                && in_array(OTPService::ROLE_TWO_FACTOR_SUCCEED, $currentToken->getRoleNames(), true)) {

                // start device session
                $this->deviceSession->createSession($user);
            }
        }
    }
}