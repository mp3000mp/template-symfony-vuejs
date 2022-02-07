<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;

class UsersControllerTest extends AbstractControllerTest
{
    private function getUserId(string $username): int
    {
        return $this->em->getRepository(User::class)->findOneBy(['username' => $username])->getId();
    }

    public function testIndexOk(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $this->client->request('GET', '/api/users');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => 'user']);

        self::assertCount(3, $jsonResponse);
        self::assertEquals([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'isEnabled' => $user->getIsEnabled(),
            'roles' => $user->getRoles(),
        ], $jsonResponse[0]);
    }

    public function testShowOk(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('GET', "/api/users/$id");

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('user', $jsonResponse['username']);
    }

    public function testRoles(): void
    {
        $this->loginUser($this->client);

        $id = $this->getUserId('user');

        $this->client->request('GET', '/api/users');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->client->request('GET', "/api/users/$id");
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->client->request('POST', '/api/users');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->client->request('PUT', "/api/users/$id");
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->client->request('PUT', "/api/users/$id/enable");
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->client->request('PUT', "/api/users/$id/disable");
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->client->request('DELETE', "/api/users/$id");
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateOk(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $this->client->request('POST', '/api/users', [], [], [], json_encode([
            'email' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('test', $jsonResponse['username']);
        self::assertEmailCount(0);
        // todo check bdd

        // enabled
        $this->client->request('POST', '/api/users', [], [], [], json_encode([
            'email' => 'test2@mp3000.fr',
            'isEnabled' => true,
            'roles' => ['ROLE_USER'],
            'username' => 'test2',
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('test2', $jsonResponse['username']);
        self::assertEmailCount(1);
        // todo check bdd
    }

    public function testCreateBadRequest(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $this->client->request('POST', '/api/users', [], [], [], json_encode([
            'badProperty' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('Invalid request content.', $jsonResponse['message']);
    }

    public function testCreateDuplicate(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $this->client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'user',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertStringContainsString('Entity does not validate.', $jsonResponse['message']);
        self::assertStringContainsString('[username=', $jsonResponse['message']);
        self::assertStringContainsString('[email=', $jsonResponse['message']);
    }

    public function testUpdateOk(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('PUT', "/api/users/$id", [], [], [], json_encode([
            'email' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('test', $jsonResponse['username']);
        self::assertEmailCount(0);

        // enabled
        $id = $this->getUserId('test');
        $this->client->request('PUT', "/api/users/$id", [], [], [], json_encode([
            'email' => 'test2@mp3000.fr',
            'isEnabled' => true,
            'roles' => ['ROLE_USER'],
            'username' => 'test2',
        ]));

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('test2', $jsonResponse['username']);
        self::assertEmailCount(1);
        // todo check bdd
    }

    public function testUpdateBadRequest(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('PUT', "/api/users/$id", [], [], [], json_encode([
            'badProperty' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('Invalid request content.', $jsonResponse['message']);
    }

    public function testUpdateDuplicate(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('PUT', "/api/users/$id", [], [], [], json_encode([
            'email' => 'admin@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'admin',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertStringContainsString('Entity does not validate.', $jsonResponse['message']);
        self::assertStringContainsString('[username=', $jsonResponse['message']);
        self::assertStringContainsString('[email=', $jsonResponse['message']);
    }

    public function testUpdateDisableSelf(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('admin');
        $this->client->request('PUT', "/api/users/$id", [], [], [], json_encode([
            'email' => 'admin@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_ADMIN'],
            'username' => 'admin',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('You cannot disable yourself.', $jsonResponse['message']);
    }

    public function testEnableOk(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('disabled');
        $this->client->request('PUT', "/api/users/$id/enable");

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals(true, $jsonResponse['isEnabled']);
        self::assertEmailCount(1);
    }

    public function testEnableAlready(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('PUT', "/api/users/$id/enable");

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('This user is already enabled.', $jsonResponse['message']);
        self::assertEmailCount(0);
    }

    public function testDisableOk(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('PUT', "/api/users/$id/disable");

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals(false, $jsonResponse['isEnabled']);
    }

    public function testDisableAlready(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('disabled');
        $this->client->request('PUT', "/api/users/$id/disable");

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('This user is already disabled.', $jsonResponse['message']);
    }

    public function testDisableSelf(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('admin');
        $this->client->request('PUT', "/api/users/$id/disable");

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('You cannot disable yourself.', $jsonResponse['message']);

        $this->client->request('PUT', "/api/users/$id", [], [], [], json_encode([
            'email' => 'admin@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'admin',
        ]));

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('You cannot disable yourself.', $jsonResponse['message']);
    }

    public function testDelete200(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('disabled');
        $this->client->request('DELETE', "/api/users/$id");

        self::assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testDelete400(): void
    {
        $this->loginUser($this->client, 'ROLE_ADMIN');

        $id = $this->getUserId('user');
        $this->client->request('DELETE', "/api/users/$id");

        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        self::assertEquals('You cannot delete an enabled user.', $jsonResponse['message']);
    }
}
