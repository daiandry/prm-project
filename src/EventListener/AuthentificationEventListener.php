<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\EventListener;

use App\Entity\PrmAdministration;
use App\Entity\PrmProfil;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\Container;
use App\Utils\ConstantSrv;
use App\Entity\User;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class AuthentificationEventListener
 * @package Parasol\CommunBundle\EventListener
 */
class AuthentificationEventListener
{

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     *
     * @return JsonResponse
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {

        $data = $event->getData();
        $user = $event->getUser();
        $userAdmin = $user->getAdministration();
        $idTypeAdmin = 0;

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor();
        if ($propertyAccessor->getValue($user, 'firstLogin') === null) {
            $event->setData(['code' => 200, 'message' => 'Refresh token ok', 'data' => $data]);
            return;
        }
        if ($user->getFirstLogin()) {
            $event->setData(['code' => 400, 'message' => 'First connexion']);
            return;
        }
        $token = $data['token'] ?? null;
        $ttl = (isset($_ENV["JWT_TOKEN_TTL"])) ? $_ENV["JWT_TOKEN_TTL"] : 3600;
        if (!is_null($token)) {
            $oToken = $this->container->get('lexik_jwt_authentication.encoder')->decode($token);
            $iat = $oToken['iat'];
            $exp = $oToken['exp'];
            $ttl = $exp - $iat;
        }

        $sTtl = $user->getLocked() == "true" ? null : $ttl;
        $sDateExpireTtl = $user->getLocked() == "true" ? null : date('Y-m-d H:i:s', $exp);
        $response = array(
            'code' => ConstantSrv::CODE_SUCCESS,
            'message' => "Success request",
            'data' => array_merge(
                array(
                    'user_id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'enabled' => $user->isEnabled(),
                    'nom' => $user->getNom(),
                    'first_login' => $user->getFirstLogin(),
                    'last_login' => !is_null($user->getLastLogin()) ? $user->getLastLogin() : null,
                    'jwt_token_ttl' => $sTtl,
                    'jwt_token_date_exp' => $sDateExpireTtl,
                    'droits' => $user->getRoles(),
                    'profil' => ($user->getProfil() instanceof PrmProfil) ? $user->getProfil()->getId() : null,
                    'idTypeAdmin' => ($userAdmin instanceof PrmAdministration) ? $userAdmin->getTypeAdmin()->getId() : 1,
                    'locked' => $user->getLocked(),
                    'photo' => $this->getPhoto($user)
                ), $data
            )
        );
        if ($user->getLocked()) {
            $response['data']['token'] = null;
        }
        $user->setEnabled(true);
        $user->setTn(null);
        $user->setLastLogin(new \DateTime('now', new \DateTimeZone('Indian/Antananarivo')));
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $em->persist($user);
        $em->flush();
        $event->setData($response);
    }

    /**
     * @param AuthenticationFailureEvent $event
     *
     * @throws \Exception
     * @return JsonResponse
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $response = new JsonResponse();
        if (!$event->getException()->getToken()) {
            $event->setResponse($response->setData(['code' => 403, 'message' => 'Refresh token invalid']));
            return;
        }
        $username = $event->getException()->getToken()->getUsername();

        $rep = $this->container
            ->get('doctrine.orm.entity_manager')
            ->getRepository(User::class);
        $user = $rep->findOneBy(array('username' => $username));
        if ($user instanceof User) {
            if ($user->getFirstLogin()) {
                $response->setData(['code' => 400, 'message' => 'First connexion']);
                $event->setResponse($response);
                return;
            }
        }
        $reponse = $this->checkUserTentativeFalse($user);
        $response->setData($reponse);
        $event->setResponse($response);
    }

    /**
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $expiration = new \DateTime('+1 day');
        $expiration->setTime(2, 0, 0);

        $payload = $event->getData();
        $payload['exp'] = $expiration->getTimestamp();

        $event->setData($payload);
    }

    public function checkUserTentativeFalse($user)
    {
        $date_now = new \DateTime("now", new \DateTimeZone('Indian/Antananarivo'));
        $em = $this->container->get('doctrine')->getManager();
        if ($user instanceof User) {
            $userTn = $user->getTn();
            if (($userTn != NULL) && ($userTn >= 3)) {
                $response = array(
                    'code' => 407,
                    'message' => "Echec tentative expirée",
                    'http_message' => 'Authentication failed'
                );
            } else {
                $inc = ($userTn == NULL) ? 1 : (intval($userTn) + 1);
                $tnRestant = 3 - intval($inc);
                $user->setTn($inc);
                $user->setLastLogin($date_now);
                if ($tnRestant == 0) {
                    $this->blocquerStatutUser($user);
                }
                $em->persist($user);
                $em->flush();
                $response = array(
                    'code' => 406,
                    'message' => "Login ou mot de passe incorrect",
                    'data' => array(
                        'tentative_restant' => $tnRestant
                    )
                );
            }
        } else {
            $response = array(
                'code' => 401,
                'message' => "Login ou mot de passe incorrect",
                'http_message' => 'Authentication failed'
            );
        }

        return $response;
    }

    /**
     * @param ParasolUser $user
     * @return bool
     */
    public function checkNumberTentative(User $user)
    {
        $val = false;
        $userTn = $user->getTn();
        if (($userTn != NULL) && ($userTn >= 3)) {
            $val = true;
        } else {
            $user->setTn(NULL);
            $em = $this->container->get('doctrine')->getManager();
            $em->persist($user);
            $em->flush();
            $val = false;
        }
        return $val;
    }

    /**
     * @param ParasolUser $user
     * @return ParasolUser
     */
    public function blocquerStatutUser(User $user)
    {
        $user->setLocked(true);

        return $user;
    }

    private function getPhoto($data)
    {
        $photo = $data->getPhoto();
        $photoUser = [];
        if ($photo) {
            $file = @file_get_contents($photo->getChemin());
            $base64 = base64_encode($file);
            $photoUser['chemin'] = $base64;
            $photoUser['nom'] = $photo->getNom();
            $photoUser['mimetype'] = $photo->getMimetype();

        }
        return $photoUser;
    }
}