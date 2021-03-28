<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Loader;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerTest extends WebTestCase
{

    private $userByRole = [
        'ROLE_USER' => 'user',
        'ROLE_ADMIN' => 'admin',
    ];

    protected function setUp(): void
    {

        /*$loader = new Loader();
        $loader->addFixture(new AppFixtures());

        $em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();


        */
        parent::setUp();
    }

    protected function loginUser(KernelBrowser $client, string $role = 'ROLE_USER'): void
    {
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => $this->userByRole[$role]]);
        $client->loginUser($testUser);
    }

    protected function getResponseJson(Response $response): array
    {
        return json_decode($response->getContent(), true);
    }
}
