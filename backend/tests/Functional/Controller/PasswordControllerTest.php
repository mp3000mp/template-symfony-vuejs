<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;

class PasswordControllerTest extends AbstractControllerTest
{
    private function generateForgottenPasswordToken(string $username): string
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        $user->generateResetPasswordToken();
        $this->em->persist($user);
        $this->em->flush();

        return $user->getResetPasswordToken();
    }

    public function testForgottenPasswordSendOk(): void
    {
        $this->client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'user@mp3000.fr']));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEmailCount(1);
    }

    public function testForgottenPasswordSendUnknownEmail(): void
    {
        $this->client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'unknown@mp3000.fr']));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEmailCount(0);
    }

    public function testForgottenPasswordSendDisabled(): void
    {
        $this->client->request('POST', '/api/password/forgotten', [], [], [], json_encode(['email' => 'disabled@mp3000.fr']));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEmailCount(0);
    }

    public function testForgottenPasswordCheck200(): void
    {
        $goodToken = $this->generateForgottenPasswordToken('user');

        // forgotten
        $this->client->request('GET', "/api/password/forgotten/$goodToken");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // init
        $this->client->request('GET', "/api/password/init/$goodToken");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testForgottenPasswordCheck404(): void
    {
        $goodToken = $this->generateForgottenPasswordToken('disabled');

        // forgotten
        $this->client->request('GET', '/api/password/forgotten/badToken');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', "/api/password/forgotten/$goodToken");
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        // init
        $this->client->request('GET', '/api/password/init/badToken');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', "/api/password/init/$goodToken");
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testForgottenPasswordReset200(): void
    {
        $goodToken = $this->generateForgottenPasswordToken('user');
        $this->client->request('POST', "/api/password/forgotten/$goodToken", [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test3000!',
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $goodToken = $this->generateForgottenPasswordToken('user');
        $this->client->request('POST', "/api/password/init/$goodToken", [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test3000!',
        ]));
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testForgottenPasswordCheckExpiredToken(): void
    {
        $this->client->request('GET', '/api/password/forgotten/badToken');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This token has expired.', $jsonResponse['message']);

        $this->client->request('GET', '/api/password/init/badToken');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This token has expired.', $jsonResponse['message']);
    }

    public function testForgottenPasswordResetBadToken(): void
    {
        $goodToken = $this->generateForgottenPasswordToken('user');

        $this->client->request('POST', '/api/password/forgotten/badToken', [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test3000!',
        ]));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This token has expired.', $jsonResponse['message']);

        $this->client->request('POST', '/api/password/init/badToken', [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test3000!',
        ]));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This token has expired.', $jsonResponse['message']);
    }

    public function testForgottenPasswordResetBadConfirm(): void
    {
        $goodToken = $this->generateForgottenPasswordToken('user');

        $this->client->request('POST', "/api/password/forgotten/$goodToken", [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test4000!',
        ]));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('Password confirmation is different.', $jsonResponse['message']);

        $this->client->request('POST', "/api/password/init/$goodToken", [], [], [], json_encode([
            'password' => 'Test3000!',
            'passwordConfirm' => 'Test4000!',
        ]));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('Password confirmation is different.', $jsonResponse['message']);
    }

    public function testForgottenPasswordResetInvalid(): void
    {
        $goodToken = $this->generateForgottenPasswordToken('user');

        $this->client->request('POST', "/api/password/forgotten/$goodToken", [], [], [], json_encode([
            'password' => '1234',
            'passwordConfirm' => '1234',
        ]));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This password is not strong enough.', $jsonResponse['message']);

        $this->client->request('POST', "/api/password/init/$goodToken", [], [], [], json_encode([
            'password' => '1234',
            'passwordConfirm' => '1234',
        ]));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This password is not strong enough.', $jsonResponse['message']);
    }

    public function testResetPassword200(): void
    {
        $this->loginUser($this->client);

        $this->client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'Test2000!',
            'newPassword' => 'Test3000!',
            'newPassword2' => 'Test3000!',
        ]));

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testResetPasswordBadConfirm(): void
    {
        $this->loginUser($this->client);

        $this->client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'Test2000!',
            'newPassword' => 'validPassword',
            'newPassword2' => 'badConfirmation',
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('Password confirmation is different.', $jsonResponse['message']);
    }

    public function testResetPasswordBadCurrent(): void
    {
        $this->loginUser($this->client);

        $this->client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'badPassword',
            'newPassword' => 'Test3000!',
            'newPassword2' => 'Test3000!',
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('Authentication failed.', $jsonResponse['message']);
    }

    public function testResetPasswordInvalid(): void
    {
        $this->loginUser($this->client);

        $this->client->request('POST', '/api/password/reset', [], [], [], json_encode([
            'currentPassword' => 'Test2000!',
            'newPassword' => '1234',
            'newPassword2' => '1234',
        ]));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $jsonResponse = $this->getResponseJson($this->client->getResponse());
        $this->assertEquals('This password is not strong enough.', $jsonResponse['message']);
    }
}
