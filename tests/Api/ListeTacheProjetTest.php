<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 17/11/2020
 * Time: 13:52
 */

namespace App\Tests\Api;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\PrmProjet;

class ListeTacheProjetTest extends ApiTestCase
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
            array('json'=>["email" => $_ENV['LOGIN_USER_TEST'],"password" => $_ENV['PWD_USER_TEST']],'headers'=>['content-type' => 'application/json', 'accept' => 'application/json'])

        );
        $this->token = json_decode($this->client->getResponse()->getContent(), true)['data']['token'];

    }

    public function testListTacheProjet()
    {
        $this->getToken();
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/projet/2/taches',[
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'authorization' => 'Bearer '.$this->token
            ]
        ]);

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON
        $this->assertResponseHeaderSame('content-type', 'application/json');

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(PrmProjet::class);
    }
}