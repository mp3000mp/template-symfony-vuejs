<?php

namespace App\Tests\Controller;

use App\Entity\User;

/**
 * Class SecurityControllerTest
 * @package App\Tests\Controller
 */
class SecurityControllerTest extends AbstractController
{
    /**
     * @dataProvider urlProvider
     */
    public function testAppUrlsAnonym($url, $codeAnonym, $codeRoleUser)
    {
        $crawler = $this->client->request('GET', $url);
        $this->debug500($url, $crawler);
        $this->assertEquals($codeAnonym, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testAppUrlsRoleUser($url, $codeAnonym, $codeRoleUser)
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
            ['/', 302, 200],
            ['/login', 200, 302],
            //['/forgottenpass/matthias.perret@qodelis.fr', 302, 200],
            //['/resetpass/token', 302, 200],
            ['/profile', 302, 200],
	        //['/logincheck', 302, 302], todo
            ['/logout', 302, 302],
            ['/user', 302, 200],
            ['/user/1/show', 302, 200],
            //['/user/new', 302, 200], todo
	        //['/user/1/edit', 302, 200], todo
	        ['/api/contact/new', 302, 200],
	        ['/api/contact/1/edit', 302, 200],
	        ['/api/contact/1/delete', 302, 200],
	        ['/api/visit', 302, 200],
	        ['/api/visit/new', 302, 200],
	        ['/api/visit/1/edit', 302, 200],
	        ['/api/visit/1/start', 302, 200],
	        ['/api/visit/1/delete', 302, 200],
	        ['/api/visit/2/product/new', 302, 200],
	        ['/api/visit/2/product/1/edit', 302, 200],
	        ['/api/visit/2/product/1/delete', 302, 200],
        ];
    }

    /**
     * @dataProvider forgottenPassProvider
     */
    public function testForgottenPass($email, $code)
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
            ['matthias.perret@qodelis.fr', 200], // todo tester envoi mail + tester date resettoken db
        ];
    }

    /**
     * @dataProvider resetPassRedirectProvider
     */
    public function testResetPassRedirect($token, $code)
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
    public function testResetPassSuccess()
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
