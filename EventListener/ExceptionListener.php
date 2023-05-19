<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\EventListener;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use FOS\RestBundle\Exception\InvalidParameterException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use App\Utils\ConstantSrv;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Created by PhpStorm.
 * User: daandry
 * Date: 12/04/2019
 * Time: 09:16
 */
class ExceptionListener
{
    const DEFAULT_ERROR_STATUS_CODE = 200;

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $path = $event->getRequest()->getPathInfo();
        $exception = $event->getThrowable();
        if ($_ENV['APP_ENV'] == "dev") {
            return;
        }
        if (preg_match('#^/api/#', $path)) {
            $response = $this->handleException($exception);
            if (!is_null($response)) {
                $event->setResponse($response);
            }
        }
    }

    /**
     * @param \Exception $exception
     *
     * @return null|JsonResponse
     * @throws \Exception
     */
    public function handleException(\Exception $exception)
    {
        $response = new JsonResponse();
        if ($exception instanceof InvalidParameterException) { // parametre invalide
            $response->setData(
                array_merge(
                    array(
                        'message' => $exception->getMessage(),
                        'http_message' => 'Parametre invalid',
                        'code' => ConstantSrv::CODE_UNAUTHORIZED,
                    ), $this->getMessageParts($exception)
                )
            );
        } elseif ($exception instanceof DisabledException) { // User account is disabled.
            $response->setData(
                array_merge(
                    array(
                        'message' => $exception->getMessage(),
                        'http_message' => 'User account is disabled.',
                        'code' => ConstantSrv::CODE_UNAUTHORIZED,
                    ), $this->getMessageParts($exception)
                )
            );
        } elseif ($exception instanceof NotFoundHttpException) { // url Introuvable
            $response->setData(
                array_merge(
                    array(
                        'message' => $exception->getMessage(),
                        'http_message' => 'Not found',
                        'code' => ConstantSrv::CODE_DATA_NOTFOUND,
                    ), $this->getMessageParts($exception)
                )
            );
        } elseif ($exception instanceof JWTEncodeFailureException) {
            $response->setData(
                array_merge(
                    array(
                        'message' => $exception->getMessage(),
                        'http_message' => 'Authentication failed',
                        'code' => ConstantSrv::CODE_INTERNAL_ERROR,
                    ), $this->getMessageParts($exception)
                )
            );
        } elseif ($exception instanceof BadRequestHttpException) {
            $response->setData(
                array_merge(
                    array(
                        'message' => $exception->getMessage(),
                        'http_message' => 'Authentication failed',
                        'code' => ConstantSrv::CODE_BAD_REQUEST,
                    ), $this->getMessageParts($exception)
                )
            );
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $response->setData(
                array_merge(
                    array(
                        'message' => $exception->getMessage(),
                        'http_message' => 'Method Not Allowed',
                        'code' => ConstantSrv::CODE_METHODE_NOTFOUND,
                    ), $this->getMessageParts($exception)
                )
            );
        } elseif ($exception instanceof UniqueConstraintViolationException) {
            $messageExeption = $exception->getMessage();
            if (preg_match('#Duplicate entry \'(.*)\'#Uis', $messageExeption, $val)) {
                $messageExeption = $val[1];
            }
            $response->setData(
                array(
                    'message' => "L'élément  " . (string)$messageExeption . " existe déjà !",
                    'http_message' => 'Duplicate entry',
                    'code' => ConstantSrv::CODE_INTERNAL_ERROR,
                )
            );
        } else if ($exception instanceof AccessDeniedHttpException) {
            $messageExeption = $exception->getMessage();
            $response->setData(
                array(
                    'message' => $messageExeption,
                    'http_message' => 'Action non autorisée',
                    'code' => ConstantSrv::CODE_UNAUTHORIZED_METHODE
                )
            );
        } else if ($exception instanceof ValidationException) {
            $messageExeption = $exception->getMessage();
            $response->setData(
                array(
                    'message' => $messageExeption,
                    'http_message' => 'Erreur interne',
                    'code' => ConstantSrv::CODE_BAD_REQUEST
                ));
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        } else if ($exception instanceof \Exception) {
            $messageExeption = $exception->getMessage();
            $response->setData(
                array(
                    'message' => $messageExeption,
                    'http_message' => 'Erreur interne',
                    'code' => ConstantSrv::CODE_INTERNAL_ERROR
                )
            );
        } else {
            $response = null;
        }

        return $response;
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    protected
    function getMessageParts(\Exception $exception)
    {
        $message = $exception->getMessage();
        $customResponse = array(
            'message' => $message,
        );
        if (strpos($message, '|') !== false) {
            list($title, $message,) = explode('|', $message);
            $customResponse['message'] = $message;
            $customResponse['title'] = $title;
        }

        return $customResponse;
    }
}