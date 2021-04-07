<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserControllerTest extends AbstractControllerTest
{

    private function getUserId(KernelBrowser $client, string $username): int
    {
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository(User::class)->findOneBy(['username' => $username])->getId();
    }

    public function testMeOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/api/me');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('user', $jsonResponse['username']);
    }

    public function testIndexOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $client->request('GET', '/api/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertCount(2, $jsonResponse);
    }

    public function testShowOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'user');
        $client->request('GET', "/api/users/$id");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('user', $jsonResponse['username']);
    }

    public function testRoles():void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $id = $this->getUserId($client,'user');

        $client->request('GET', '/api/users');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->request('GET', "/api/users/$id");
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->request('PUT', '/api/users');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->request('POST', "/api/users/$id");
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->request('POST', "/api/users/$id/enable");
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->request('POST', "/api/users/$id/disable");
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        $client->request('DELETE', "/api/users/$id");
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'email' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('test', $jsonResponse['username']);
        $this->assertEmailCount(0);
        // todo check bdd

        // enabled
        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'email' => 'test2@mp3000.fr',
            'isEnabled' => true,
            'roles' => ['ROLE_USER'],
            'username' => 'test2',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('test2', $jsonResponse['username']);
        $this->assertEmailCount(1);
        // todo check bdd
    }

    public function testCreateBadRequest(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        // disabled
        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'badProperty' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('Invalid request content.', $jsonResponse['detail']);
    }

    public function testCreateDuplicate(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        // disabled
        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'email' => 'user@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'user',
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertStringContainsString('Entity does not validate.', $jsonResponse['detail']);
        $this->assertStringContainsString('[username=', $jsonResponse['detail']);
        $this->assertStringContainsString('[email=', $jsonResponse['detail']);
    }

    public function testUpdateOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'user');
        $client->request('POST', "/api/users/$id", [], [], [], json_encode([
            'email' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('test', $jsonResponse['username']);
        $this->assertEmailCount(0);
        // todo check bdd

        // enabled
        $client->request('POST', '/api/users/1', [], [], [], json_encode([
            'email' => 'test2@mp3000.fr',
            'isEnabled' => true,
            'roles' => ['ROLE_USER'],
            'username' => 'test2',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('test2', $jsonResponse['username']);
        $this->assertEmailCount(1);
        // todo check bdd
    }

    public function testUpdateBadRequest(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'user');
        $client->request('POST', "/api/users/$id", [], [], [], json_encode([
            'badProperty' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('Invalid request content.', $jsonResponse['detail']);
    }

    public function testUpdateDuplicate(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'user');
        $client->request('POST', "/api/users/$id", [], [], [], json_encode([
            'email' => 'admin@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'admin',
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertStringContainsString('Entity does not validate.', $jsonResponse['detail']);
        $this->assertStringContainsString('[username=', $jsonResponse['detail']);
        $this->assertStringContainsString('[email=', $jsonResponse['detail']);
    }

    public function testEnableOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'disabled');
        $client->request('POST', "/api/users/$id/enable");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals(true, $jsonResponse['isEnabled']);
        $this->assertEmailCount(1);
    }

    public function testEnableAlready(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'user');
        $client->request('POST', "/api/users/$id/enable");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertStringContainsString('already', $jsonResponse['message']);
        $this->assertEmailCount(0);
    }

    public function testDisableOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'disabled');
        $client->request('POST', "/api/users/$id/enable");

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals(false, $jsonResponse['isEnabled']);
    }

    public function testDisableSelf(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'admin');
        $client->request('POST', "/api/users/$id/disable");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertStringContainsString('yourself', $jsonResponse['message']);

        $client->request('POST', '/api/users/1', [], [], [], json_encode([
            'email' => 'admin@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'admin',
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertStringContainsString('yourself', $jsonResponse['message']);
    }

    public function testDelete200(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'disabled');
        $client->request('DELETE', "/api/users/$id");

        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDelete400(): void
    {
        $client = static::createClient();
        $this->loginUser($client, 'ROLE_ADMIN');

        $id = $this->getUserId($client,'user');
        $client->request('DELETE', "/api/users/$id");

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertStringContainsString('enabled', $jsonResponse['message']);
    }
}
