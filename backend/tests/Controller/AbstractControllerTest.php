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
        $client->request(
            'POST',
            '/api/logincheck',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $testUser->getUsername(),
                'password' => 'Test2000!',
            ])
        );
        $data = json_decode($client->getResponse()->getContent(), true);
        
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));
    }

    protected function getResponseJson(Response $response): array
    {
        return json_decode($response->getContent(), true);
    }
}
