<?php

namespace App\Tests\Controller;

class PasswordControllerTest extends AbstractControllerTest
{
    public function testForgottenPasswordSendOk(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'user@mp3000.fr']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEmailCount(1);
    }

    public function testForgottenPasswordSendUnknownEmail(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'unknown@mp3000.fr']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEmailCount(0);
    }

    public function testForgottenPasswordSendDisabled(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'disabled@mp3000.fr']));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEmailCount(0);
    }

    public function testForgottenPasswordCheck200(): void
    {
        $client = static::createClient();

        // todo good token ?

        $client->request('GET', '/api/password/forgotten/goodToken');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // todo good token ?

        $client->request('GET', '/api/password/init/goodToken');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testForgottenPasswordCheck404(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/password/forgotten/badToken');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        // todo good token user disabled ?

        $client->request('GET', '/api/password/forgotten/goodToken');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->request('GET', '/api/password/init/badToken');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        // todo good token user disabled ?

        $client->request('GET', '/api/password/init/goodToken');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testForgottenPasswordReset200(): void
    {
        $client = static::createClient();

        // todo good token ?

        $client->request('POST', '/api/password/forgotten/goodToken', [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test3000!',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // todo good token ?

        $client->request('POST', '/api/password/init/goodToken', [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test3000!',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testForgottenPasswordCheckExpiredToken(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/password/forgotten/badToken');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('This token has expired.', $jsonResponse['message']);

        $client->request('GET', '/api/password/init/badToken');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('This token has expired.', $jsonResponse['message']);
    }

    public function testForgottenPasswordResetInvalid(): void
    {
        $client = static::createClient();

        // todo good token ?

        $client->request('POST', '/api/password/forgotten/goodToken', [], [], [], json_encode(['password' => '1234']));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('This password is not strong enough.', $jsonResponse['message']);

        // todo good token ?

        $client->request('POST', '/api/password/init/goodToken', [], [], [], json_encode(['password' => '1234']));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('This password is not strong enough.', $jsonResponse['message']);
    }

    public function testResetPassword200(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // todo logged ?

        $client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'Test2000!',
            'newPassword' => 'Test3000!',
            'newPassword2' => 'Test3000!',
        ]));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testResetPasswordBadConfirm(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // todo logged ?

        $client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'Test2000!',
            'newPassword' => 'validPassword',
            'newPassword2' => 'badConfirmation',
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('Password confirmation is different.', $jsonResponse['message']);
    }

    public function testResetPasswordBadCurrent(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // todo logged ?

        $client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'badPassword',
            'newPassword' => 'Test3000!',
            'newPassword2' => 'Test3000!',
        ]));

        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('Authentication failed.', $jsonResponse['message']);
    }

    public function testResetPasswordInvalid(): void
    {
        $client = static::createClient();
        $this->loginUser($client);

        // todo logged ?

        $client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'Test2000!',
            'newPassword' => '1234',
            'newPassword2' => '1234',
        ]));

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($client->getResponse());
        $this->assertEquals('This password is not strong enough.', $jsonResponse['message']);
    }
}
