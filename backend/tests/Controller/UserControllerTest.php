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
}
