<?php

namespace App\Service\OTP;

use App\Entity\User;
use App\Security\TwoFactorAuthSubscriber;
use OTPHP\TOTP;
use OTPHP\TOTPInterface;
use ParagonIE\ConstantTime\Base32;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class OTPService
 * @package App\Service\Noshare
 */
class OTPService{

    public const IMG_URL = 'https://assets.gitlab-static.net/uploads/-/system/user/avatar/2876944/avatar.png';
    public const ROLE_TWO_FACTOR_SUCCEED = 'TWO_FACTOR_SUCCEED';
    public const ROUTE_TWO_FACTOR = 'two_factor';

    /** @var string */
    private $secret;

    /**
     * @param User $user
     *
     * @return TOTPInterface
     */
    public function getUserOTP(User $user): TOTPInterface
    {
        if($user->getTwoFactorSecret() === null){
            return $this->generateNewOtp($user);
        }else{
            return $this->createOTP($user->getTwoFactorSecret(), $user);
        }
    }

    /**
     * @param User $user
     *
     * @return TOTPInterface
     */
    public function generateNewOtp(User $user)
    {
        return $this->createOTP($this->generateSecret(), $user);
    }

    /**
     * @param string $secret
     * @param User $user
     *
     * @return TOTPInterface
     */
    private function createOTP(string $secret, User $user)
    {
        $otp = TOTP::create($secret);
        $otp->setLabel($user->getUsername());
        $otp->setIssuer('Template');
        $otp->setParameter('image', self::IMG_URL);
        return $otp;
    }

    /**
     * @return string
     */
    public function generateSecret(): string
    {
        $this->secret = trim(Base32::encodeUpper(hash('sha256', uniqid('Template-SF', true))),'=');;
        return $this->secret;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     */
    public function addTwoFactorRole(TokenStorageInterface $tokenStorage, SessionInterface $session): void
    {
        /** @var PostAuthenticationGuardToken $currentToken */
        $currentToken = $tokenStorage->getToken();
        $roles = array_merge($currentToken->getRoleNames(), [self::ROLE_TWO_FACTOR_SUCCEED]);
        $newToken = new PostAuthenticationGuardToken($currentToken->getUser(), $currentToken->getProviderKey(), $roles);
        $tokenStorage->setToken($newToken);
        $session->set('_security_' . $currentToken->getProviderKey(), serialize($newToken));
    }

}
