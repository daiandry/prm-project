<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 13/11/2020
 * Time: 16:14
 */

namespace App\Tests\Controller;


use App\Tests\Fonctions;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{

    public function testLoginSuccess()
    {
        $headers = Fonctions::HEADERS;
        $client = static::createClient();
        $client->request(
            'POST',
            '/authentication_token',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'accept' => 'application/json'),
            '{"email":"'.$_ENV['LOGIN_USER_TEST'].'","password":"'.$_ENV['PWD_USER_TEST'].'"}'
        );

        $resp = json_decode($client->getResponse()->getContent(), true);
        self::assertResponseIsSuccessful();
        $this->assertEquals(200, $resp['code']);
        
    }

    public function testLoginFailure()
    {
        $headers = Fonctions::HEADERS;
        $client = static::createClient();
        $client->request(
            'POST',
            '/authentication_token',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'accept' => 'application/json'),
            '{"email":"bidon@test.com","password":"123456"}'
        );

        $resp = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(401, $resp['code']);
        self::assertResponseIsSuccessful();
    }
}