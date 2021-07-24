<?php

namespace App\Tests\Functional\Controller;

class ExceptionControllerTest extends AbstractControllerTest
{
    public function test404(): void
    {
        $this->client->request('GET', '/api/does/not/exist');

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('No route found for "GET http://localhost/api/does/not/exist"', $jsonResponse['message']);
    }
}
