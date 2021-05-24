<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\AppFixtures;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerTest extends WebTestCase
{
    private array $userByRole = [
        'ROLE_USER' => 'user',
        'ROLE_ADMIN' => 'admin',
    ];

    protected EntityManagerInterface $em;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');

        // utils
        $this->em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // reset database
        $purger = new ORMPurger($this->em, []);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $loader = new ContainerAwareLoader(self::$kernel->getContainer());
        $loader->addFixture(new AppFixtures());
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());

        // parent
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        unset($this->em);
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
            [],
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
