<?php

declare(strict_types=1);

namespace App\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerManageProject implements NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    private $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $docs = $this->decorated->normalize($object, $format, $context);

        $docs['components']['schemas']['PrmProjet'] = [
            'type' => 'object',
            'properties' => [
                'nom' => [
                    'type' => 'string',
                    'example' => 'nom projet',
                ],
                'conv_cl' => [
                    'type' => 'string',
                    'example' => 'CONV CL',
                ],
                'projet_parent_id' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'coordonnee_gps' => [
                    'type' => 'array',
                    'example' => [
                        "latitude" => -18.553457950671927,
                        "longitude" => 45.9949815273285
                    ],
                ],
                'localite_emplacement' => [
                    'type' => 'integer',
                    'example' => [12710],
                ],
                'engagement' => [
                    'type' => 'integer',
                    'example' => [
                        "id" => 25,
                        "libelle" => 'libelle'
                    ],
                ],
                'statut' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'categorie' => [
                    'type' => 'array',
                    'example' => [
                        "id" => 1,
                        "libelle" => 'PIP'
                    ],
                ],
                'soa_code' => [
                    'type' => 'string',
                    'example' => 'SOA_CODE',
                ],
                'pcop_code' => [
                    'type' => 'string',
                    'example' => 'pcop code',
                ],
                'description' => [
                    'type' => 'string',
                    'example' => 'description du projet',
                ],
                'prommesse_presidentielle' => [
                    'type' => 'string',
                    'example' => 'promesse présidentielle',
                ],
                'projet_inaugurable' => [
                    'type' => 'boolean',
                    'example' => 0,
                ],
                'priorite' => [
                    'type' => 'integer',
                    'example' => [
                        "id" => 25,
                        "libelle" => 'libelle'
                    ],
                ],
                'date_inauguration' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'secteur' => [
                    'type' => 'integer',
                    'example' => [
                        "id" => 25,
                        "libelle" => 'libelle'
                    ],
                ],
                'type' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'collectivite_territoriale_descentralisee' => [
                    'type' => 'string',
                    'example' => 'collectivite territoriale descentralisee',
                ],

                'pdm_date_debut_appel_offre' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_fin_offre' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_signature_contrat' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_titulaire_du_marche' => [
                    'type' => 'array',
                    'example' => [
                        'id' => 1,
                        'nom' => 'colas',
                        'contact' => '+261340000000',
                    ],
                ],
                'pdm_designation' => [
                    'type' => 'string',
                    'example' => 'pdm designation',
                ],
                'pdm_tiers_nif' => [
                    'type' => 'string',
                    'example' => 'nif',
                ],
                'pdm_date_lancement_os' => [
                    'type' => 'string',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_lancement_travaux_prevu' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_lancement_travaux_reel' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_delai_execution_prevu' => [
                    'type' => 'string',
                    'example' => '10',
                ],
                'pdm_date_fin_prevu' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],

                'rf_date_signature_autorisation_engagement' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'rf_autorisation_engagement' => [
                    'type' => 'integer',
                    'example' => 1000,
                ],
                'rf_credit_payement_annee_en_cours' => [
                    'type' => 'string',
                    'example' => '1000',
                ],
                'rf_montant_depenses_decaisees_mandate' => [
                    'type' => 'string',
                    'example' => '1000',
                ],
                'rf_montant_depenses_decaisees_liquide' => [
                    'type' => 'string',
                    'example' => '1000',
                ],
                'rf_exercice_budgetaire' => [
                    'type' => 'string',
                    'example' => 'exercice',
                ],
                'rf_montant_global_projet' => [
                    'type' => 'float',
                    'example' => 10000000,
                ],
                'situation_actuelle' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'avancement' => [
                    'type' => 'integer',
                    'example' => 95,
                ],
                'observation' => [
                    'type' => 'string',
                    'example' => 'observation',
                ],
                'photos' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "statut" => "statut",
                            "description" => "description de la photo",
                            "nom" => "mcd_prm.jpg",
                            "mimetype" => "application/jpg",
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/2wBDAAgGBg"
                        ]
                    ],
                ],
                'document' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "statut" => "statut",
                            "description" => "description de la photo",
                            "type" => 2,
                            "nom" => "mcd_prm.jpg",
                            "mimetype" => "application/jpg",
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/2wBDAAgGBg"
                        ],
                        [
                            "statut" => "statut",
                            "description" => "description de la photo",
                            "type" => 1,
                            "nom" => "mcd_prm.jpg",
                            "mimetype" => "application/jpg",
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/2wBDAAgGBg"
                        ]
                    ],
                ],
            ],
        ];

        $createProjetDocumentation = [
            'paths' => [
                '/api/createProjet' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Création d\'un projet',
                        'requestBody' => [
                            'description' => 'Création d\'un projet',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/PrmProjet',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessRequest',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_NOT_FOUND => [
                                'description' => 'zone_not_found',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ZoneNotFound',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_CONFLICT => [
                                'description' => 'duplicate_project',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Conflict',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        //request success
        $docs['components']['schemas']['SuccessRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
            ],
        ];
        //request zone not found
        $docs['components']['schemas']['ZoneNotFound'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_NOT_FOUND,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'zone_not_found',
                ]
            ],
        ];
        //request duplicata
        $docs['components']['schemas']['Conflict'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_CONFLICT,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'duplicate_project',
                ]
            ],
        ];

        //liste type zone
        $docs['components']['schemas']['TypeZone'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "code" => 'FOKOTANY',
                            "libelle" => "FOKOTANY"
                        ],
                    ]
                ],
            ],
        ];


        $listTypeZoneDocumentation = [
            'paths' => [
                '/api/listTypeZone' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste les types de zone',
                        'requestBody' => [
                            'description' => 'Liste les types de zone',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TypeZone',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        //Liste des engagements
        $docs['components']['schemas']['Engagement'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'La paix et la sécurité durable pour tous',
                        ],
                    ]
                ],
            ],
        ];
        $listEngagementDocumentation = [
            'paths' => [
                '/api/listEngagement' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des engagements',
                        'requestBody' => [
                            'description' => 'Liste des engagements',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Engagement',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $listTypeProjetDocumentation = [
            'paths' => [
                '/api/listTypeProjet' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste les type de projets',
                        'requestBody' => [
                            'description' => 'Liste les type de projets',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Engagement',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste secteur
        $docs['components']['schemas']['Secteur'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'Gouvernance',
                        ],
                    ]
                ],
            ],
        ];
        $listsecteurDocumentation = [
            'paths' => [
                '/api/listSecteur' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des secteurs',
                        'requestBody' => [
                            'description' => 'Liste des secteurs',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Secteur',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste categorie projet
        $docs['components']['schemas']['CategorieProjet'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'Urgent',
                        ],
                    ]
                ],
            ],
        ];
        $listCatProjetDocumentation = [
            'paths' => [
                '/api/listCategorie' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des categories projet',
                        'requestBody' => [
                            'description' => 'Liste des categories projet',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/CategorieProjet',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste titulaire du marcher
        $docs['components']['schemas']['TitulaireDuMarcherProjet'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "nom" => 'colas',
                            "contact" => '+261340000000',
                        ],
                    ]
                ],
            ],
        ];
        $listTitulaireProjetDocumentation = [
            'paths' => [
                '/api/listTitulaireMarcher' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des titulaires du marcher',
                        'requestBody' => [
                            'description' => 'Liste des titulaires du marcher',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TitulaireDuMarcherProjet',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste type admin
        $docs['components']['schemas']['TypeAdministration'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'Ministère',
                            "code" => 'MINISTERE',
                        ],
                    ]
                ],
            ],
        ];
        $listTypeAdministrationDocumentation = [
            'paths' => [
                '/api/listTypeAdministration' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste type d\'administration',
                        'requestBody' => [
                            'description' => "Liste type d\'administration",
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TypeAdministration',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste situation projet
        $docs['components']['schemas']['SituationProjet'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'DAO et affichage en attente validation CNM',
                        ],
                    ]
                ],
            ],
        ];
        $listSituationDocumentation = [
            'paths' => [
                '/api/listSituationProjet' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des situations projet',
                        'requestBody' => [
                            'description' => 'Liste des situations projet',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SituationProjet',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste priotite projet success
        $docs['components']['schemas']['PrioriteProjet'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'Urgent',
                        ],
                    ]
                ],
            ],
        ];
        //list priorite projet
        $listPrioriteDocumentation = [
            'paths' => [
                '/api/listPriorite' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des priorités projet',
                        'requestBody' => [
                            'description' => 'Liste des priorités projet',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/PrioriteProjet',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste statut projet success
        $docs['components']['schemas']['StatutProjet'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'A faire',
                        ],
                    ]
                ],
            ],
        ];
        //list statut projet
        $listStatutDocumentation = [
            'paths' => [
                '/api/listStatutProjet' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste des statuts de projet',
                        'requestBody' => [
                            'description' => 'Liste des statuts de projet',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/StatutProjet',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //liste type de document
        $docs['components']['schemas']['TypeDoc'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => 'Appel d\'offre',
                        ],
                    ]
                ],
            ],
        ];
        $listTypeDocDocumentation = [
            'paths' => [
                '/api/listTypeDocument' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste type des documents',
                        'requestBody' => [
                            'description' => 'Liste type des documents',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        //'$ref' => '#/components/schemas/Projet/list',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TypeDoc',
                                        ],
                                    ],

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // liste les fils d'une zone en question
        $docs['components']['schemas']['ProjetId'] = [
            'type' => 'object',
            'properties' => [
                'projet_id' => [
                    'type' => 'integer',
                    'example' => 1,
                ]
            ],
        ];

        //request list zone success
        $docs['components']['schemas']['SuccessListProjetRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        "list" => [
                            [
                                "id" => 37,
                                "engagement" => 1,
                                "engagement_libelle" => "La paix et la sécurité durable pour tous",
                                "secteur" => 1,
                                "secteur_libelle" => "Gouvernance",
                                "type" => 1,
                                "type_libelle" => "Travaux",
                                "nom" => "projet_14",
                                "conv_cl" => "CONV CL",
                                "collectivite_territoriale_descentralisee" => "collecte",
                                "pdm_date_fin_appel_offre" => "2020-10-08 11:48:40",
                                "pdm_date_signature_contrat" => "2020-10-08 11:48:40",
                                "pdm_titulaire_du_marche" => 1,
                                "pdm_designation" => "designer par",
                                "pdm_tiers_nif" => "00011112220021",
                                "pdm_date_lancement_os" => "2020-10-08 11:48:40",
                                "pdm_date_lancement_travaux_prevu" => "2020-10-08 11:48:40",
                                "pdm_date_travaux_reel" => "2020-10-08 11:48:40",
                                "pdm_delai_execution_prevu" => "30",
                                "pdm_date_fin_prevu" => "2020-10-08 11:48:40",
                                "rf_date_signature" => "2020-10-08 11:48:40",
                                "avancement" => 100,
                                "observation" => "observation",
                                "soa_code" => "SOA_15",
                                "pcop_compte" => "pcode",
                                "promesse_presidentielle" => "je te promet",
                                "inaugurable" => false,
                                "en_retard" => null,
                                "date_inauguration" => "2020-09-24 15:52:31",
                                "priorite" => 1,
                                "priorite_libelle" => "Urgent",
                                "statut" => 3,
                                "statut_libelle" => "terminé",
                                "categorie" => [
                                    "id" => 1,
                                    "libelle" => "PIP"
                                ],
                                "categorie_libelle" => "PIP",
                                "pdm_date_debut_appel_offre" => "2020-10-08 11:48:40",
                                "rf_date_signature_autorisation_engagement" => "2020-10-08 11:48:40",
                                "rf_autorisation_engagement" => "1000",
                                "rf_credit_payement_annee_en_cours" => "1000",
                                "rf_montant_depenses_decaissess_mandate" => "1000",
                                "rf_montant_depenses_decaissess_liquide" => "1000",
                                "rf_exercice_budgetaire" => "exo",
                                "rf_budget_consomme" => 1000000000,
                                "situation_projet" => 1,
                                "situation_projet_libelle" => "DAO et affichage en attente validation CNM",
                                "description" => "description project",
                                "coordonnegps" => [
                                    "latitude" => -18.553457950671927,
                                    "longitude" => 45.9949815273285
                                ],
                                "projet_parent_id" => 29,
                                "projet_parent_nom" => "projet",
                                "created_by" => 1,
                                "created_by_email" => "admin@admin.com"
                            ],
                            [
                                "id" => 38,
                                "engagement" => 1,
                                "engagement_libelle" => "La paix et la sécurité durable pour tous",
                                "secteur" => 1,
                                "secteur_libelle" => "Gouvernance",
                                "type" => 1,
                                "type_libelle" => "Travaux",
                                "nom" => "projet_14",
                                "conv_cl" => "CONV CL",
                                "collectivite_territoriale_descentralisee" => "collecte",
                                "pdm_date_fin_appel_offre" => "2020-10-08 11:48:40",
                                "pdm_date_signature_contrat" => "2020-10-08 11:48:40",
                                "pdm_titulaire_du_marche" => 1,
                                "pdm_designation" => "designer par",
                                "pdm_tiers_nif" => "00011112220021",
                                "pdm_date_lancement_os" => "2020-10-08 11:48:40",
                                "pdm_date_lancement_travaux_prevu" => "2020-10-08 11:48:40",
                                "pdm_date_travaux_reel" => "2020-10-08 11:48:40",
                                "pdm_delai_execution_prevu" => "30",
                                "pdm_date_fin_prevu" => "2020-10-08 11:48:40",
                                "rf_date_signature" => "2020-10-08 11:48:40",
                                "avancement" => 100,
                                "observation" => "observation",
                                "soa_code" => "SOA_16",
                                "pcop_compte" => "pcode",
                                "promesse_presidentielle" => "je te promet",
                                "inaugurable" => false,
                                "en_retard" => null,
                                "date_inauguration" => "2020-09-24 15:52:31",
                                "priorite" => 1,
                                "priorite_libelle" => "Urgent",
                                "categorie" => [
                                    "id" => 1,
                                    "libelle" => "PIP"
                                ],
                                "categorie_libelle" => "PIP",
                                "pdm_date_debut_appel_offre" => "2020-10-08 11:48:40",
                                "rf_date_signature_autorisation_engagement" => "2020-10-08 11:48:40",
                                "rf_autorisation_engagement" => "1000",
                                "rf_credit_payement_annee_en_cours" => "1000",
                                "rf_montant_depenses_decaissess_mandate" => "1000",
                                "rf_montant_depenses_decaissess_liquide" => "1000",
                                "rf_exercice_budgetaire" => "exo",
                                "rf_budget_consomme" => 1000000000,
                                "situation_projet" => 1,
                                "situation_projet_libelle" => "DAO et affichage en attente validation CNM",
                                "description" => "description project",
                                "coordonnegps" => [
                                    "latitude" => -18.553457950671927,
                                    "longitude" => 45.9949815273285
                                ],
                                "projet_parent_id" => 29,
                                "projet_parent_nom" => "projet",
                                "created_by" => 1,
                                "created_by_email" => "admin@admin.com"
                            ]
                        ],
                        "total" => 2
                    ]
                ],
            ],
        ];

        //request list zone success
        $docs['components']['schemas']['SuccessProjetByIdRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        "id" => 37,
                        "engagement" => 1,
                        "engagement_libelle" => "La paix et la sécurité durable pour tous",
                        "secteur" => 1,
                        "secteur_libelle" => "Gouvernance",
                        "type" => 1,
                        "type_libelle" => "Travaux",
                        "nom" => "projet_14",
                        "conv_cl" => "CONV CL",
                        "collectivite_territoriale_descentralisee" => "collecte",
                        "pdm_date_fin_appel_offre" => "2020-10-08 11:48:40",
                        "pdm_date_signature_contrat" => "2020-10-08 11:48:40",
                        "pdm_titulaire_du_marche" => 1,
                        "pdm_designation" => "designer par",
                        "pdm_tiers_nif" => "00011112220021",
                        "pdm_date_lancement_os" => "2020-10-08 11:48:40",
                        "pdm_date_lancement_travaux_prevu" => "2020-10-08 11:48:40",
                        "pdm_date_travaux_reel" => "2020-10-08 11:48:40",
                        "pdm_delai_execution_prevu" => "30",
                        "pdm_date_fin_prevu" => "2020-10-08 11:48:40",
                        "rf_date_signature" => "2020-10-08 11:48:40",
                        "avancement" => 100,
                        "observation" => "observation",
                        "soa_code" => "SOA_15",
                        "pcop_compte" => "pcode",
                        "promesse_presidentielle" => "je te promet",
                        "inaugurable" => false,
                        "en_retard" => null,
                        "date_inauguration" => "2020-09-24 15:52:31",
                        "priorite" => 1,
                        "priorite_libelle" => "Urgent",
                        "statut" => 3,
                        "statut_libelle" => "terminé",
                        "categorie" => [
                            "id" => 1,
                            "libelle" => "PIP"
                        ],
                        "categorie_libelle" => "PIP",
                        "pdm_date_debut_appel_offre" => "2020-10-08 11:48:40",
                        "rf_date_signature_autorisation_engagement" => "2020-10-08 11:48:40",
                        "rf_autorisation_engagement" => "1000",
                        "rf_credit_payement_annee_en_cours" => "1000",
                        "rf_montant_depenses_decaissess_mandate" => "1000",
                        "rf_montant_depenses_decaissess_liquide" => "1000",
                        "rf_exercice_budgetaire" => "exo",
                        "situation_projet" => 1,
                        "situation_projet_libelle" => "DAO et affichage en attente validation CNM",
                        "description" => "description project",
                        "coordonnegps" => [
                            "latitude" => -18.553457950671927,
                            "longitude" => 45.9949815273285
                        ],
                        "projet_parent_id" => 29,
                        "projet_parent_nom" => "projet",
                        "created_by" => 1,
                        "created_by_email" => "admin@admin.com",
                        "localite_emplacement" => [
                            [
                                "id" => 12520,
                                "libelle" => "Amboanana",
                                "type_id" => 4
                            ]
                        ],
                        "photos" => [
                            "id" => 14,
                            "chemin" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_photos/",
                            "nom" => "20201110_072956100419_mcd_prm.jpg",
                            "chemin_complet" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_photos/20201110_072956100419_mcd_prm.jpg",
                            "upload_date" => [
                                "date" => "2020-11-10 07:29:56.000000",
                                "timezone_type" => 3,
                                "timezone" => "UTC"
                            ],
                            "description" => "description de la photo",
                            "statut" => "statut",
                            "mimetype" => null,
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/"
                        ],
                        "document" => [
                            [
                                "id" => 20,
                                "chemin" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_doc/",
                                "nom" => "20201110_072956103067_mcd_prm.jpg",
                                "chemin_complet" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_doc/20201110_072956103067_mcd_prm.jpg",
                                "upload_date" => [
                                    "date" => "2020-11-10 07:29:56.000000",
                                    "timezone_type" => 3,
                                    "timezone" => "UTC"
                                ],
                                "description" => "description de la photo",
                                "statut" => "statut",
                                "mimetype" => null,
                                "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/"
                            ],
                            [
                                "id" => 21,
                                "chemin" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_doc/",
                                "nom" => "20201110_072956128204_base64file.txt",
                                "chemin_complet" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_doc/20201110_072956128204_base64file.txt",
                                "upload_date" => [
                                    "date" => "2020-11-10 07:29:56.000000",
                                    "timezone_type" => 3,
                                    "timezone" => "UTC"
                                ],
                                "description" => "base64 file",
                                "statut" => "statut",
                                "mimetype" => null,
                                "value" => "LzlqLzRBQVFTa1pKUmdBQk"
                            ]
                        ]
                    ]
                ],
            ],
        ];

        //request list zone success
        $docs['components']['schemas']['SuccessListProjetParentRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 34,
                            "nom" => "projet",
                        ],
                    ]
                ],
            ],
        ];

        //get les informations d'un projet de reference
        $getProjetByIdParentDocumentation = [
            'paths' => [
                '/api/getProjetById' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Renvoie les informations d\'un projet de reference',
                        'requestBody' => [
                            'description' => 'Renvoie les informations d\'un projet de reference',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ProjetId',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessProjetByIdRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // paramettre liste projet
        $docs['components']['schemas']['ListProjetParameter'] = [
            'type' => 'object',
            'properties' => [
                "nom" => [
                    'type' => "string",
                    'example' => "projet",
                ],
                "localite_emplacement" => [
                    'type' => "integer",
                    'example' => 12520,
                ],
                "type_zone" => [
                    'type' => "string",
                    'example' => 2,
                ],
                "statut" => [
                    'type' => "string",
                    'example' => 1,
                ],
                "en_retard" => [
                    'type' => "boolean",
                    'example' => 0,
                ],
                "projet_inaugurable" => [
                    'type' => "boolean",
                    'example' => 1,
                ],
                "pdm_date_fin_prevu_debut" => [
                    'type' => "datetime",
                    'example' => "2020-10-08 11:48:40",
                ],
                "pdm_date_fin_prevu_fin" => [
                    'type' => "datetime",
                    'example' => "2020-10-08 11:48:40",
                ],
                "type_administration" => [
                    'type' => "integer",
                    'example' => 1,
                ],
                "administration" => [
                    'type' => "integer",
                    'example' => 1,
                ],
                "engagement" => [
                    'type' => "integer",
                    'example' => 1,
                ],
                "secteur" => [
                    'type' => "integer",
                    'example' => 1,
                ],
                'page' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'itemsPerPage' => [
                    'type' => 'integer',
                    'example' => 10,
                ]
            ],
        ];

        //Liste tous les projets
        $getListProjetDocumentation = [
            'paths' => [
                '/api/listProjet' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste tous les projets',
                        'requestBody' => [
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ListProjetParameter',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessListProjetRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //Liste tous les projets parent
        $getListProjetParentDocumentation = [
            'paths' => [
                '/api/listProjetParent' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste tous les projets parent',
                        'requestBody' => [
                            'description' => 'Liste tous les projets parent',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessListProjetParentRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //Download un fichier
        $downloadFileDocumentation = [
            'paths' => [
                '/api/downloadFile/{fileId}' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Download un fichier',
                        'requestBody' => [
                            'description' => 'Download un fichier',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            //'$ref' => '#/components/schemas/SuccessListProjetParentRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        // liste les fils d'une zone en question
        $docs['components']['schemas']['PhotosProjetId'] = [
            'type' => 'object',
            'properties' => [
                'projet_id' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'doc' => [
                    'type' => 'boolean',
                    'example' => true,
                ]
            ],
        ];

        //request list file project success
        $docs['components']['schemas']['SuccessListFileProjetRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 14,
                            "chemin" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_photos/",
                            "nom" => "20201110_072956100419_mcd_prm.jpg",
                            "chemin_complet" => "C:\\wamp64\\www\\plan-PRM-back/upload/project_photos/20201110_072956100419_mcd_prm.jpg",
                            "upload_date" => [
                                "date" => "2020-11-10 07:29:56.000000",
                                "timezone_type" => 3,
                                "timezone" => "UTC"
                            ],
                            "description" => "description de la photo",
                            "statut" => "statut",
                            "mimetype" => null,
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAA"
                        ],
                    ]
                ],
            ],
        ];

        //retourne les fichiers d'un projet
        $getFileProjetDocumentation = [
            'paths' => [
                '/api/getFileByIdProjet' => [
                    'get' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'retourne les fichiers d\'un projet',
                        'requestBody' => [
                            'description' => 'retourne les fichiers d\'un projet',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/PhotosProjetId',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessListFileProjetRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //edit projet
        $docs['components']['schemas']['EditProjet'] = [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'example' => 'identifiant du projet',
                ],
                'nom' => [
                    'type' => 'string',
                    'example' => 'nom projet',
                ],
                'conv_cl' => [
                    'type' => 'string',
                    'example' => 'CONV CL',
                ],
                'projet_parent_id' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'coordonnee_gps' => [
                    'type' => 'array',
                    'example' => [
                        "latitude" => -18.553457950671927,
                        "longitude" => 45.9949815273285
                    ],
                ],
                'localite_emplacement' => [
                    'type' => 'integer',
                    'example' => [12710],
                ],
                'engagement' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'statut' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'categorie' => [
                    'type' => 'array',
                    'example' => [
                        "id" => 1,
                        "libelle" => 'PIP'
                    ],
                ],
                'soa_code' => [
                    'type' => 'string',
                    'example' => 'SOA_CODE',
                ],
                'pcop_code' => [
                    'type' => 'string',
                    'example' => 'pcop code',
                ],
                'description' => [
                    'type' => 'string',
                    'example' => 'description du projet',
                ],
                'prommesse_presidentielle' => [
                    'type' => 'string',
                    'example' => 'promesse présidentielle',
                ],
                'projet_inaugurable' => [
                    'type' => 'boolean',
                    'example' => 0,
                ],
                'priorite' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'date_inauguration' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'secteur' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'type' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'collectivite_territoriale_descentralisee' => [
                    'type' => 'string',
                    'example' => 'collectivite territoriale descentralisee',
                ],

                'pdm_date_debut_appel_offre' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_fin_offre' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_signature_contrat' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_titulaire_du_marche' => [
                    'type' => 'array',
                    'example' => [
                        'id' => 1,
                        'nom' => 'colas',
                        'contact' => '+261340000000',
                    ],
                ],
                'pdm_designation' => [
                    'type' => 'string',
                    'example' => 'pdm designation',
                ],
                'pdm_tiers_nif' => [
                    'type' => 'string',
                    'example' => 'nif',
                ],
                'pdm_date_lancement_os' => [
                    'type' => 'string',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_lancement_travaux_prevu' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_date_lancement_travaux_reel' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],
                'pdm_delai_execution_prevu' => [
                    'type' => 'string',
                    'example' => '10',
                ],
                'pdm_date_fin_prevu' => [
                    'type' => 'datetime',
                    'example' => '2020-10-08 11:48:40',
                ],

                'rf_date_signature_autorisation_engagement' => [
                    'type' => 'string',
                    'example' => '2020-10-08 11:48:40',
                ],
                'rf_autorisation_engagement' => [
                    'type' => 'integer',
                    'example' => 1000,
                ],
                'rf_credit_payement_annee_en_cours' => [
                    'type' => 'string',
                    'example' => '1000',
                ],
                'rf_montant_depenses_decaisees_mandate' => [
                    'type' => 'string',
                    'example' => '1000',
                ],
                'rf_montant_depenses_decaisees_liquide' => [
                    'type' => 'string',
                    'example' => '1000',
                ],
                'rf_exercice_budgetaire' => [
                    'type' => 'string',
                    'example' => 'exercice',
                ],
                'rf_montant_global_projet' => [
                    'type' => 'float',
                    'example' => 10000000,
                ],
                'situation_actuelle' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'avancement' => [
                    'type' => 'integer',
                    'example' => 95,
                ],
                'observation' => [
                    'type' => 'string',
                    'example' => 'observation',
                ],
                'photos' => [
                    'type' => 'object',
                    'example' => [
                        "id" => 24,
                        "statut" => "statut",
                        "description" => "description de la photo",
                        "nom" => "mcd_prm.jpg",
                        "mimetype" => "application/jpg",
                        "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/2wBDAAgGBg"
                    ],
                ],
                'document' => [
                    'type' => 'string',
                    'example' => [
                        [
                            "id" => 43,
                            "statut" => "statut",
                            "description" => "description de la photo",
                            "type" => 2,
                            "nom" => "mcd_prm.jpg",
                            "mimetype" => "application/jpg",
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/2wBDAAgGBg"
                        ],
                        [
                            "id" => 44,
                            "statut" => "statut",
                            "description" => "description de la photo",
                            "type" => 1,
                            "nom" => "mcd_prm.jpg",
                            "mimetype" => "application/jpg",
                            "value" => "/9j/4AAQSkZJRgABAgAAAQABAAD/2wBDAAgGBg"
                        ]
                    ],
                ],
            ],
        ];

        //request projet not found
        $docs['components']['schemas']['ProjetNotFound'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_NOT_FOUND,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'projet_not_found',
                ]
            ],
        ];

        //modification du projet
        $editProjetDocumentation = [
            'paths' => [
                '/api/editProjet' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'modification d\'un projet',
                        'requestBody' => [
                            'description' => 'modification d\'un projet',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/EditProjet',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessRequest',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_NOT_FOUND => [
                                'description' => 'projet_not_found',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/ProjetNotFound',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //request list projet hostory success
        $docs['components']['schemas']['SuccessListProjetHistoryRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 5,
                            "classe_name" => "PrmProjet",
                            "metadata" => [
                                "date_modify" => [
                                    "20/11/2020",
                                    "20/11/2020"
                                ],
                                "observation" => [
                                    "observation 1",
                                    "observation 2"
                                ],
                                "zone" => [],
                                "0" => []
                            ],
                            "user" => [
                                "id" => 1,
                                "email" => "admin@admin.com",
                                "nom" => null
                            ],
                            "created_at" => "2020-11-20T11:53:05+00:00",
                            "ressource_type" => [
                                "id" => 5,
                                "libelle" => "PrmProjet"
                            ],
                            "ressource_id" => 70
                        ]
                    ]
                ],
            ],
        ];

        //Liste tous les historiques de projet
        $getListProjetHistoryDocumentation = [
            'paths' => [
                '/api/listProjetHistory' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste tous les historiques projet',
                        'requestBody' => [
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ProjetId',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessListProjetHistoryRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        //Liste tous les observation par projet
        $getListProjetObservationDocumentation = [
            'paths' => [
                '/api/getObservationByProjetId' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Liste tous les observation par projet',
                        'requestBody' => [
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ProjetId',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessListObsRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $docs['components']['schemas']['SuccessListObsRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 4,
                            "user" => [
                                "id" => 1,
                                "email" => "admin@admin.com",
                                "nom" => null
                            ],
                            "projet" => [
                                "id" => 79,
                                "nom" => "projet_28"
                            ],
                            "date_update" => "2020-11-26T05:56:29+00:00",
                            "old_val" => null,
                            "new_val" => "observation"
                        ]
                    ]
                ],
            ],
        ];

        //Liste tous les observation par projet
        $setValidationProjetDocumentation = [
            'paths' => [
                '/api/validationProjet' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Envoie du projet au prochain instance',
                        'requestBody' => [
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ValidationParameter',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/SuccessValidationRequest',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $docs['components']['schemas']['ValidationParameter'] = [
            'type' => 'object',
            'properties' => [
                'profil_id' => [
                    'type' => 'array',
                    'example' => [
                        9,
                        10
                    ],
                ],
                'projet_id' => [
                    'type' => 'integer',
                    'example' => 70,
                ]
            ],
        ];

        $docs['components']['schemas']['SuccessValidationRequest'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
            ],
        ];

        //save secteur api
        $saveSecteurDocumentation = [
            'paths' => [
                '/api/saveSecteur' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Creation et modification Secteur',
                        'requestBody' => [
                            'description' => 'Creation et modification Secteur',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/secteurObjet',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/configurationRsult',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['secteurObjet'] = [
            'type' => 'object',
            'properties' => [
                'secteur' => [
                    'type' => 'string',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => "PIP"
                        ],
                        [
                            "id" => 2,
                            "libelle" => "Plan Marshall"
                        ]
                    ],
                ]
            ],
        ];
        $docs['components']['schemas']['configurationRsult'] = [
            'type' => 'object',
            'properties' => [
                'code' => [
                    'type' => 'string',
                    'example' => Response::HTTP_OK,
                ],
                'Message' => [
                    'type' => 'string',
                    'example' => 'success_request',
                ],
                'data' => [
                    'type' => 'array',
                    'example' => [
                        [
                            "id" => 4,
                            "libelle" => "Promesse présidentielle"
                        ]
                    ]
                ]
            ],
        ];

        //save categorie api
        $saveCategorieDocumentation = [
            'paths' => [
                '/api/saveCategorie' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Creation et modification categorie',
                        'requestBody' => [
                            'description' => 'Creation et modification categorie',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/categorieObjet',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/configurationRsult',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['categorieObjet'] = [
            'type' => 'object',
            'properties' => [
                'categorie' => [
                    'type' => 'string',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => "PIP"
                        ],
                        [
                            "id" => 2,
                            "libelle" => "Plan Marshall"
                        ]
                    ],
                ]
            ],
        ];

        //save engagement api
        $saveEngagementDocumentation = [
            'paths' => [
                '/api/saveEngagement' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Creation et modification engagement projet',
                        'requestBody' => [
                            'description' => 'Creation et modification engagement projet',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/engagementObjet',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/configurationRsult',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['engagementObjet'] = [
            'type' => 'object',
            'properties' => [
                'engagement' => [
                    'type' => 'string',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => "PIP"
                        ],
                        [
                            "id" => 2,
                            "libelle" => "Plan Marshall"
                        ]
                    ],
                ]
            ],
        ];

        //save priorite api
        $savePrioriteDocumentation = [
            'paths' => [
                '/api/savePriorite' => [
                    'post' => [
                        'tags' => ['PrmProjet'],
                        'summary' => 'Creation et modification priorite projet',
                        'requestBody' => [
                            'description' => 'Creation et modification priorite projet',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/prioriteObjet',
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'success_request',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/configurationRsult',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $docs['components']['schemas']['prioriteObjet'] = [
            'type' => 'object',
            'properties' => [
                'priorite' => [
                    'type' => 'string',
                    'example' => [
                        [
                            "id" => 1,
                            "libelle" => "Urgent"
                        ],
                        [
                            "id" => 2,
                            "libelle" => "Haute"
                        ]
                    ],
                ]
            ],
        ];

        return array_merge_recursive(
            $docs,
            $createProjetDocumentation,
            $listTypeZoneDocumentation,
            $listEngagementDocumentation,
            $listsecteurDocumentation,
            $listSituationDocumentation,
            $listPrioriteDocumentation,
            $listCatProjetDocumentation,
            $listTypeDocDocumentation,
            $getProjetByIdParentDocumentation,
            $getListProjetDocumentation,
            $getListProjetParentDocumentation,
            $downloadFileDocumentation,
            $getFileProjetDocumentation,
            $editProjetDocumentation,
            $listStatutDocumentation,
            $listTitulaireProjetDocumentation,
            $listTypeAdministrationDocumentation,
            $listTypeProjetDocumentation,
            $getListProjetHistoryDocumentation,
            $getListProjetObservationDocumentation,
            $setValidationProjetDocumentation,
            $saveSecteurDocumentation,
            $saveCategorieDocumentation,
            $saveEngagementDocumentation,
            $savePrioriteDocumentation);
    }
}