<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PasswordControllerTest extends WebTestCase
{
    public function testForgottenPasswordUnknownEmail(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'test@mp3000.fr']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordGet200(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/password/reset/good_token');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordGet404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/password/reset/bad_token');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordPost200(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/reset', [], [], [], json_encode(['token' => 'good_token', 'password' => 'validPassword']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordPost404(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/reset', [], [], [], json_encode(['token' => 'expired_token', 'password' => '1234']));

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordPost400(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/reset', [], [], [], json_encode(['token' => 'good_token', 'password' => '1234']));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
}
