<?php

declare(strict_types=1);

namespace App\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SwaggerZoneDecorator implements NormalizerInterface
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
        unset($docs['paths']['/api/prm_zone_geos']);
        unset($docs['paths']['/api/prm_zone_geos/{id}']);
        $docs['components']['schemas']['Zone_Geo'] = [
            'type' => 'object',
            'properties' => [
                'type_zone' => [
                    'type' => 'integer',
                    'example' => 2,
                ]
            ],
        ];

        $createTypeZoneDocumentation = [
            'paths' => [
                '/api/listZoneByType' => [
                    'post' => [
                        'tags' => ['Zone_Geo'],
                        'summary' => 'Liste des zones géo par type',
                        'requestBody' => [
                            'description' => 'Liste des zones géo par type',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/Zone_Geo',
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
                                            '$ref' => '#/components/schemas/SuccessListRequest',
                                        ],
                                    ],
                                ],
                            ],
                            Response::HTTP_NOT_FOUND => [
                                'description' => 'type_not_found',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/TypeZoneNotFound',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        //request type zone not found
        $docs['components']['schemas']['TypeZoneNotFound'] = [
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

        //request list success
        $docs['components']['schemas']['SuccessListRequest'] = [
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
                            'id' => 1,
                            'code' => '54',
                            'libelle' => 'MENABE'
                        ],
                    ]
                ],
            ],
        ];
        //request list zone success
        $docs['components']['schemas']['SuccessListZoneRequest'] = [
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
                            "id" => 12204,
                            "type_id" => 3,
                            "libelle" => "AMPARAFARAVOLA",
                            "code" => "312",
                            "parent_id" => 12179
                        ],
                    ]
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
        // liste les fils d'une zone en question
        $docs['components']['schemas']['ZoneId'] = [
            'type' => 'object',
            'properties' => [
                'zone_id' => [
                    'type' => 'integer',
                    'example' => [12520],
                ]
            ],
        ];

        // liste les fils d'une zone en question
        $docs['components']['schemas']['ZoneAndTypeId'] = [
            'type' => 'object',
            'properties' => [
                'zone_id' => [
                    'type' => 'integer',
                    'example' => 1,
                ],
                'type_id' => [
                    'type' => 'integer',
                    'example' => 2,
                ]
            ],
        ];
        $listZoneElementDocumentation = [
            'paths' => [
                '/api/listZoneElementById' => [
                    'post' => [
                        'tags' => ['Zone_Geo'],
                        'summary' => 'Liste les fils d\'une zone de référence',
                        'requestBody' => [
                            'description' => 'liste les fils d\'une zone de reference',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ZoneAndTypeId',
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
                                            '$ref' => '#/components/schemas/SuccessListZoneRequest',
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
                        ],
                    ],
                ],
            ],
        ];

        //request list zone success
        $docs['components']['schemas']['SuccessZoneByIdRequest'] = [
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
                            "zone_id" => 12520,
                            "type_id" => 4,
                            "type_libelle" => "COMMUNE",
                            "libelle_zone" => "Amboanana",
                            "code_zone" => "105503",
                            "geom" => "0106000000010000"
                        ],
                    ]
                ],
            ],
        ];

        //get zone by id
        $getZoneByIdDocumentation = [
            'paths' => [
                '/api/getZoneById' => [
                    'post' => [
                        'tags' => ['Zone_Geo'],
                        'summary' => 'Liste les informations d\'un zone de référence',
                        'requestBody' => [
                            'description' => 'liste les informations d\'un zone en question',
                            "properties" => [
                            ],
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        '$ref' => '#/components/schemas/ZoneId',
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
                                            '$ref' => '#/components/schemas/SuccessZoneByIdRequest',
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
                        ],
                    ],
                ],
            ],
        ];

        return array_merge_recursive($docs,
            $createTypeZoneDocumentation,
            $listZoneElementDocumentation,
            $getZoneByIdDocumentation);
    }
}