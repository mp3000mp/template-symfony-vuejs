<?php declare(strict_types=1);

namespace App\Service\SingleSignOn;

use App\Entity\TermsOfServiceSignature;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class SSOService
{

    /** @var string  */
    public const ROUTE_SET_TOKEN = 'sso.set_token';
    /** @var string  */
    public const GET_SP_PARAM = 'sp-url';
    /** @var string  */
    public const SESSION_SP_URL_KEY = 'cfsso_sp';

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
     * @return string
     */
    public function getSubDomain(string $url): ?string
    {
        try{
            return explode('.', explode('/', $url)[2])[0];
        }catch (\Exception $e){
            return null;
        }
    }

}
