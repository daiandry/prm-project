<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTExpiredEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class JWTListener
 * @package Parasol\CommunBundle\EventListener
 */
class JWTListener
{
    /**
     * @param JWTNotFoundEvent $event
     *
     * @throws \Exception
     */
    public function onJWTNotFound(JWTNotFoundEvent $event)
    {
        $response = new JsonResponse();
        $response->setData(
            array(
                'code'         => 403,
                'message'      => "Missing or invalid token",
                'http_message' => 'Authentication failed'
            )
        );
        $event->setResponse($response);
    }

    /**
     * @param JWTExpiredEvent $event
     */
    public function onJWTExpired(JWTExpiredEvent $event)
    {
        $response = new JsonResponse();
        $response->setData(
            array(
                'code'         => 403,
                'message'      => "Missing or invalid token",
                'http_message' => 'Authentication failed'
            )
        );
        $event->setResponse($response);
    }

    /**
     * @param JWTInvalidEvent $event
     *
     * @throws \Exception
     */
    public function onJWTInvalid(JWTInvalidEvent $event)
    {
        $response = new JsonResponse();
        $response->setData(
            array(
                'code'         => 403,
                'message'      => "Missing or invalid token",
                'http_message' => 'Authentication failed'
            )
        );
        $event->setResponse($response);
    }
}