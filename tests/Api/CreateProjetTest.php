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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CreateProjetTest extends WebTestCase
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
            '{"email":"' . $_ENV['LOGIN_USER_TEST'] . '","password":"' . $_ENV['PWD_USER_TEST'] . '"}'
        );
//        dump($this->client->getResponse()->getContent());die;
        $this->token = json_decode($this->client->getResponse()->getContent(), true)['data']['token'];

    }

    /**
     *
     */
    public function testCreateProjet()
    {
        $this->getToken();
        $this->client->request('POST', '/api/createProjet', [], [], array(
            'headers' => ['Content-Type' => 'application/json', 'accept' => 'application/json'], 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token), '{
    "nom": "projet stade mahamasina",
    "conv_cl": "conv cl mahamasina",
    "projet_parent_id": 4,
    "coordonnee_gps": {
        "latitude": "-18.902670514578677",
        "longitude": "47.530189218780514"
    },
    "localite_emplacement": [12520,12723],
    "engagement": 1,
    "categorie": {
        "id": 5,
        "libelle": "Autre"
    },
    "soa_code": "codeSOA MAHASINA192",
    "pcop_compte": "PCOPmahamasina",
    "description": "sdfsdfsd",
    "prommesse_presidentielle": 1,
    "projet_inaugurable": 1,
    "date_inauguration": "2020-11-26 00:00:00",
    "secteur": 2,
    "type": 1,
    "priorite": null,
    "pdm_date_debut_appel_offre": "2020-11-26 00:00:00",
    "pdm_date_fin_offre": "2020-11-26 00:00:00",
    "pdm_date_signature_contrat": "2020-11-26 00:00:00",
    "pdm_titulaire_du_marche": {
        "id": 0,
        "nom": "nick",
        "contact": "0340030300"
    },
    "pdm_designation": "sdfvcvsd",
    "pdm_tiers_nif": "4584521211244542",
    "pdm_date_lancement_os": "2020-10-08 11:48:40",
    "pdm_date_lancement_travaux_prevu": "2020-11-26 00:00:00",
    "pdm_date_lancement_travaux_reel": "2020-11-26 00:00:00",
    "pdm_delai_execution_prevu": "2020-11-26 00:00:00",
    "pdm_date_fin_prevu": null,
    "rf_date_signature_autorisation_engagement": "2020-10-08 11:48:40",
    "rf_autorisation_engagement": "78789754564545",
    "rf_credit_payement_annee_en_cours": "45454561",
    "rf_montant_depenses_decaisees_mandate": "221212",
    "rf_montant_depenses_decaisees_liquide": "546454545",
    "rf_exercice_budgetaire": "14112121",
    "situation_projet": 1,
    "avancement": 0,
    "statut": 1,
    "observation": "exemple nixk",
    "photos": [],
    "document": []
}'
        );

        $this->assertResponseStatusCodeSame(200, $this->client->getResponse()->getStatusCode());
        self::assertResponseIsSuccessful();
    }
}