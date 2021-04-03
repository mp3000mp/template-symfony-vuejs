<?php

namespace App\Tests\Controller;

class SecurityControllerTest extends AbstractControllerTest
{
    public function testLoginOk(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/logincheck', [], [], [], json_encode([
            'username' => 'user',
            'password' => 'Test2000!',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginBadCredentials(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/logincheck', [], [], [], json_encode([
            'username' => 'user',
            'password' => 'badPassword',
        ]));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('Invalid credentials.', $jsonResponse['message']);
    }

    public function testLoginDisabled(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/logincheck', [], [], [], json_encode([
            'username' => 'disabled',
            'password' => 'Test2000!',
        ]));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('Your accound is disabled.', $jsonResponse['message']);
    }

    public function testRefreshTokenOk(): void
    {
        $client = static::createClient();

        // todo good token ?

        $client->request('POST', '/api/token/refresh', [], [], [], json_encode([
            'refreshToken' => 'goodToken',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertArrayHasKey('token', $jsonResponse);
        $this->assertArrayHasKey('refreshToken', $jsonResponse);
    }

    public function testRefreshTokenBadToken(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/token/refresh', [], [], [], json_encode([
            'refreshToken' => 'badToken',
        ]));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('An authentication exception occurred.', $jsonResponse['message']);
    }
}
