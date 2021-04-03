<?php

namespace App\Tests\Controller;

class UserControllerTest extends AbstractControllerTest
{
    public function testMeOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/api/me');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('user', $jsonResponse['username']);
    }

    public function testCreateOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // disabled
        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'email' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test'
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
            'username' => 'test2'
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
        $this->loginUser($client);

        // disabled
        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'badProperty' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test'
        ]));

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('Invalid request content.', $jsonResponse['detail']);
    }

    public function testCreateDuplicate(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // disabled
        $client->request('PUT', '/api/users', [], [], [], json_encode([
            'email' => 'user@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'user'
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertContains('Entity does not validate.', $jsonResponse['detail']);
        $this->assertContains('[username=', $jsonResponse['detail']);
        $this->assertContains('[email=', $jsonResponse['detail']);
    }

    public function testUpdateOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // disabled
        $client->request('POST', '/api/users/1', [], [], [], json_encode([
            'email' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test'
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
            'username' => 'test2'
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
        $this->loginUser($client);

        $client->request('POST', '/api/users/1', [], [], [], json_encode([
            'badProperty' => 'test@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'test'
        ]));

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals('Invalid request content.', $jsonResponse['detail']);
    }

    public function testUpdateDuplicate(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('POST', '/api/users/1', [], [], [], json_encode([
            'email' => 'user@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'user'
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertContains('Entity does not validate.', $jsonResponse['detail']);
        $this->assertContains('[username=', $jsonResponse['detail']);
        $this->assertContains('[email=', $jsonResponse['detail']);
    }

    public function testEnableOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/api/users/3/enable');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals(true, $jsonResponse['isEnabled']);
        $this->assertEmailCount(1);
    }

    public function testEnableError(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/api/users/2/enable');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertContains('already', $jsonResponse['message']);
        $this->assertEmailCount(0);
    }

    public function testDisableOk(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        $client->request('GET', '/api/users/2/disable');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertEquals(false, $jsonResponse['isEnabled']);
    }

    public function testDisableSelf(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // disabled
        $client->request('GET', '/api/users/1/disable');

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertContains('yourself', $jsonResponse['message']);

        // edit
        $client->request('POST', '/api/users/1', [], [], [], json_encode([
            'email' => 'admin@mp3000.fr',
            'isEnabled' => false,
            'roles' => ['ROLE_USER'],
            'username' => 'admin'
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());

        $this->assertContains('yourself', $jsonResponse['message']);
    }
}
