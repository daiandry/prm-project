<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class RestListener
 * @package Parasol\CommunBundle\EventListener
 */
class RestListener
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $responseHeaders = $event->getResponse()->headers;
        $responseHeaders->set('Access-Control-Allow-Headers', 'Access-Control-Allow-Origin, X-ProjectName, X-ProjectVersion, Authorization, origin, content-type, accept, x-wsse, set-cookie, x-sessid, ResponseType');
        $responseHeaders->set('Access-Control-Allow-Origin', '*');
        $responseHeaders->set('Access-Control-Expose-Headers', '*');
        $responseHeaders->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS, PATCH');
        $request = $event->getRequest();
        $method = $request->getRealMethod();
        if (strtoupper($method) === 'OPTIONS') {
            /**
             * Dirty method
             * @todo Send with symfony method
             */
            header('Access-Control-Allow-Headers: ResponseType, Access-Control-Allow-Origin, X-ProjectName, X-ProjectVersion, Authorization, origin, content-type, accept, x-wsse, set-cookie, x-sessid');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Expose-Headers', '*');
            header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS, PATCH');
            die(
            json_encode(
                array(
                    'message' => 'Accepted',
                    'status'  => 202,
                )
            )
            );
        }
    }

}
