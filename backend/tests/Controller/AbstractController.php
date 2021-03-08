<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class AbstractController.
 */
abstract class AbstractController extends WebTestCase
{
    /** @var KernelBrowser */
    protected $client;

    /** @var EntityManagerInterface */
    protected $doctrine;

    public function setUp(): void
    {
        $this->client = self::createClient();
        $this->doctrine = $this->client->getContainer()->get('doctrine')->getManager();
    }

    protected function login(array $roles): void
    {
        $session = $this->client->getContainer()->get('session');
        $securityContext = $this->client->getContainer()->get('security.token_storage');
        $user = $this->doctrine->getRepository(User::class)->find(1);
        $firewallName = 'main';
        $token = new UsernamePasswordToken($user, null, $firewallName, $roles);
        $securityContext->setToken($token);
        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    /**
     * @param $crawler
     */
    protected function debug500(string $url, $crawler): void
    {
        if ('500' == $this->client->getResponse()->getStatusCode()) {
            echo "\n -Url: {$url} => ".$crawler->filter('div.exception-message-wrapper')->text();
            exit();
        }
    }
}
