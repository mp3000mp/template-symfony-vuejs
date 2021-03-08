<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;

/**
 * Class SecurityControllerTest.
 */
class SecurityControllerTest extends AbstractController
{
    /**
     * @dataProvider urlProvider
     */
    public function testAppUrlsAnonym(string $url, int $codeAnonym, int $codeRoleUser): void
    {
        $crawler = $this->client->request('GET', $url);
        $this->debug500($url, $crawler);
        $this->assertEquals($codeAnonym, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAppUrlsRoleUser(string $url, int $codeAnonym, int $codeRoleUser): void
    {
        $this->login(['ROLE_USER']);
        $crawler = $this->client->request('GET', $url);
        $this->debug500($url, $crawler);
        $this->assertEquals($codeRoleUser, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return [
            'home' => ['/', 302, 200],
            'login' => ['/login', 200, 302],
            //'password.forget' => ['/forgottenpass/matthias.perret@mp3000mp.fr', 302, 200],
            //'password.reset' => ['/resetpass/token', 302, 200],
            'account' => ['/account', 302, 200],
            //'login.check' => ['/logincheck', 302, 302], todo
            'logout' => ['/logout', 302, 302],
            'admin.users.index' => ['/admin/users', 302, 200],
            //'admin.users.show' => ['/admin/users/1/show', 302, 200],
            //'admin.user.new' => ['/user/new', 302, 200], todo
            //'admin.user.edit' => ['/user/1/edit', 302, 200], todo
        ];
    }

    /**
     * @dataProvider forgottenPassProvider
     *
     * @param mixed $email
     * @param mixed $code
     */
    public function testForgottenPass($email, $code): void
    {
        $url = 'forgottenpass/'.$email;
        $crawler = $this->client->request('GET', $url);
        $this->debug500($url, $crawler);
        $this->assertEquals($code, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function forgottenPassProvider()
    {
        return [
            ['', 302],
            ['test', 302],
            ['matthias.perret@mp3000mp.fr', 200], // todo tester envoi mail + tester date resettoken db
        ];
    }

    /**
     * @dataProvider resetPassRedirectProvider
     *
     * @param mixed $token
     * @param mixed $code
     */
    public function testResetPassRedirect($token, $code): void
    {
        $url = 'resetpass/'.$token;
        $crawler = $this->client->request('GET', $url);
        $this->debug500($url, $crawler);
        $this->assertEquals($code, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return array
     */
    public function resetPassRedirectProvider()
    {
        return [
            ['', 404],
            ['testFail', 302],
        ];
    }

    /**
     * @throws \Exception
     */
    public function testResetPassSuccess(): void
    {
        // opé base
        $token = 'testSuccess';
        $user = $this->doctrine->getRepository(User::class)->find(2);
        $user->setResetPasswordToken($token);
        $user->setResetPasswordDate(new \DateTime());
        $this->doctrine->persist($user);
        $this->doctrine->flush();

        $url = 'resetpass/'.$token;
        $crawler = $this->client->request('GET', $url);
        $this->debug500($url, $crawler);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // todo tester submit form
    }
}
