<?php declare(strict_types=1);

namespace App\Service\TermsOfService;

use App\Entity\TermsOfService;
use App\Entity\TermsOfServiceSignature;
use App\Entity\User;
use App\Repository\TermsOfServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class TOSService
{
    public const ROLE_TOS_SIGNED = 'TOS_SIGNED';
    public const ROUTE_TOS = 'tos';

    /** @var EntityManagerInterface  */
    private $em;

    /**
     * TOSService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return TermsOfService
     */
    public function getLastTOS(): TermsOfService
    {
        /** @var TermsOfServiceRepository $repTOS */
        $repTOS = $this->em->getRepository(TermsOfService::class);
        return $repTOS->findLast();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasSignedLastTOS(User $user): bool
    {
        $lastTOS = $this->getLastTOS();
        $signature = $this->em->getRepository(TermsOfServiceSignature::class)
            ->findOneBy(['user' => $user, 'terms_of_service' => $lastTOS])
        ;
        return null !== $signature;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     */
    public function addTOSSignedRole(TokenStorageInterface $tokenStorage, SessionInterface $session): void
    {
        /** @var PostAuthenticationGuardToken $currentToken */
        $currentToken = $tokenStorage->getToken();
        $roles = array_merge($currentToken->getRoleNames(), [self::ROLE_TOS_SIGNED]);
        $newToken = new PostAuthenticationGuardToken($currentToken->getUser(), $currentToken->getProviderKey(), $roles);
        $tokenStorage->setToken($newToken);
        $session->set('_security_' . $currentToken->getProviderKey(), serialize($newToken));
    }
}
