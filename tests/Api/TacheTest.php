<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 17/11/2020
 * Time: 09:29
 */

namespace App\Tests\Api;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TacheTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private $client;

    private $token;

    public function getToken()
    {
        $this->client = static::createClient();
        $this->client->request('POST',
            '/authentication_token',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'accept' => 'application/json'),
            '{"email":"'.$_ENV['LOGIN_USER_TEST'].'","password":"'.$_ENV['PWD_USER_TEST'].'"}'
        );
//        dump($this->client->getResponse()->getContent());die;
        $this->token = json_decode($this->client->getResponse()->getContent(), true)['data']['token'];

    }

    public function testGetCollection()
    {
        $this->getToken();

        $this->client->request('GET', '/api/prm_taches',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json', 'accept' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token));

        $this->assertResponseStatusCodeSame(200, $this->client->getResponse()->getStatusCode());
        self::assertResponseIsSuccessful();

    }

    public function testCreateTache()
    {

        $this->getToken();
        $this->client->request('POST', '/api/prm_taches', [], [], array(
                                    'CONTENT_TYPE' => 'application/json', 'accept' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token), '{
                                      "nom": "Lalambe tanaa",
                                      "projetId": null,
                                      "dateRealisationPrevu": "2020-11-17T06:53:03.183Z",
                                      "dateRealisationReel": "2020-11-17T06:53:03.183Z",
                                      "avancement": "12%",
                                      "statut": null,
                                      "observation": "Pas observation",
                                      "valeurPrevu": "1000",
                                      "valeurReel": "15000",
                                      "categorie":null,
                                      "typeTache": null,
                                      "photos": [
                                      ],
                                      "document": [
                                      ]
                                    }'
        );

        $this->assertResponseStatusCodeSame(200, $this->client->getResponse()->getStatusCode());
        self::assertResponseIsSuccessful();

    }

    public function testPutTache()
    {

        $this->getToken();
        $this->client->request('PUT', '/api/prm_taches/1', [], [], array('CONTENT_TYPE' => 'application/json', 'accept' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token), '{
                                      "nom": "Lalambe tanaa",
                                      "projetId": null,
                                      "dateRealisationPrevu": "2020-11-17T06:53:03.183Z",
                                      "dateRealisationReel": "2020-11-17T06:53:03.183Z",
                                      "avancement": "12%",
                                      "statut": null,
                                      "observation": "Pas observation",
                                      "valeurPrevu": "1000",
                                      "valeurReel": "15000",
                                      "categorie":null,
                                      "typeTache": null,
                                      "photos": [
                                      ],
                                      "document": [
                                      ]
                                    }'
        );

        $this->assertResponseStatusCodeSame(200, $this->client->getResponse()->getStatusCode());
        self::assertResponseIsSuccessful();

    }

}