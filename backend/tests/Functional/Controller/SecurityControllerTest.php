<?php

namespace App\Tests\Functional\Controller;

use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken;

class SecurityControllerTest extends AbstractControllerTest
{
    public function testLoginOk(): void
    {
        $this->client->request('POST', '/api/logincheck', [], [], [], json_encode([
            'username' => 'user',
            'password' => 'Test2000!',
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testLoginBadCredentials(): void
    {
        $this->client->request('POST', '/api/logincheck', [], [], [], json_encode([
            'username' => 'user',
            'password' => 'badPassword',
        ]));

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('Invalid credentials.', $jsonResponse['message']);
    }

    public function testLoginDisabled(): void
    {
        $this->client->request('POST', '/api/logincheck', [], [], [], json_encode([
            'username' => 'disabled',
            'password' => 'Test2000!',
        ]));

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('Your accound is disabled.', $jsonResponse['message']);
    }

    public function testRefreshTokenOk(): void
    {
        // generate fake token
        $goodToken = 'goodToken';
        $refreshToken = new RefreshToken();
        $refreshToken->setRefreshToken($goodToken);
        $refreshToken->setUsername('user');
        $refreshToken->setValid(new \DateTime('+2 days'));
        $this->em->persist($refreshToken);
        $this->em->flush();

        $this->client->request('POST', '/api/token/refresh', [], [], [], json_encode([
            'refreshToken' => $goodToken,
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertArrayHasKey('token', $jsonResponse);
        $this->assertArrayHasKey('refreshToken', $jsonResponse);
    }

    public function testRefreshTokenBadToken(): void
    {
        $this->client->request('POST', '/api/token/refresh', [], [], [], json_encode([
            'refreshToken' => 'badToken',
        ]));

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('An authentication exception occurred.', $jsonResponse['message']);
    }
}
