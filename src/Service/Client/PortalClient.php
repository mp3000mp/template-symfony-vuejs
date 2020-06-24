<?php

namespace App\Service\Client;

use App\Service\DeviceSession\DeviceSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Class PortalClient
 * @package App\Service\Client
 */
class PortalClient
{

    /** @var string */
    private $portalUrl;
    /** @var HttpClientInterface */
    private $client;
    /** @var string */
    private $bearer;

    /**
     * PortalClient constructor.
     *
     * @param string $portalUrl
     * @param string $appSecret
     * @param SessionInterface $session
     * @param HttpClientInterface $client
     */
    public function __construct(string $portalUrl, string $appSecret, SessionInterface $session, HttpClientInterface $client)
    {
        $this->portalUrl = $portalUrl.(substr($portalUrl,-1) === '/' ? '' : '/').'api/';
        $this->bearer = $appSecret.'.'.$session->get(DeviceSession::SESSION_TOKEN_KEY);
        $this->client = $client;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     *
     * @return ResponseInterface
     * @throws TransportExceptionInterface
     */
    public function request(string $method, string $url, array $options = [])
    {
        $options['auth_bearer'] = $this->bearer;
        return $this->client->request($method, $this->portalUrl.$url, $options);
    }

}
