<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\SharedSession\SharedSession;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Class LogoutListener
 *
 * @package App\EventSubscriber
 */
class LogoutListener implements LogoutSuccessHandlerInterface
{
    /** @var TokenStorageInterface  */
    private $tokenStorage;
    /** @var RouterInterface  */
    private $router;
    /** @var SharedSession  */
    private $sharedSession;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router, SharedSession $sharedSession)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->sharedSession = $sharedSession;
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function onLogoutSuccess(Request $request)
    {
        $currentToken = $this->tokenStorage->getToken();

        if ($currentToken instanceof PostAuthenticationGuardToken) {
            /** @var User $user */
            $user = $currentToken->getUser();
            $this->sharedSession->destroy($user);
        }
        return new RedirectResponse($this->router->generate('login'));
    }
}
