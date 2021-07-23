<?php

namespace App\Tests\Functional\Controller;

class AppControllerTest extends AbstractControllerTest
{
    public function testInfo(): void
    {
        $this->client->request('GET', '/api/info');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        $this->assertArrayHasKey('version', $jsonResponse);
    }

    public function testMeOk(): void
    {
        $this->loginUser($this->client);

        $this->client->request('GET', '/api/me');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());

        $this->assertEquals([
            'username' => 'user',
            'roles' => ['ROLE_USER'],
        ], $jsonResponse);
    }
}
