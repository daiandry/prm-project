<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 29/12/2020
 * Time: 14:16
 */

namespace App\Controller;


use App\Repository\UserRepository;
use App\Utils\Fonctions;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/authentication", name="authentication", methods={"POST"})
     */
    public function authenticate(UserRepository $userRepository, JWTTokenManagerInterface $JWTManager, AuthenticationSuccessHandler $authenticationSuccessHandler, AuthenticationFailureHandler $authenticationFailureHandler, Request $request, TokenStorageInterface $tokenStorage)
    {
        $password = $request->get('password');
        $email = $request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);
        $token = $tokenStorage->getToken();

        if (!$user) {
            $token = $tokenStorage->getToken();
            $token->setUser($email);
            $token->setAttributes([]);
            $token->setAuthenticated(false);
        } else {
            $isValidPassword = Fonctions::checkCredential($user, $password);
            $entityManager = $this->getDoctrine()->getManager();
            if ($isValidPassword) {
                if ($user->getLocked()) {
                    if ($usrLockExp <  new \DateTime("now", new \DateTimeZone('Indian/Antananarivo'))) {
                        $usrLockExp = $user->getLastLogin()->add(new \DateInterval('PT' . $_ENV['LOCK_EXP'] . 'S'));
                        $user->setTn(0);
                        $user->setLocked(false);
                        $entityManager->persist($user);
                        $entityManager->flush();
                    } else {
                        $exception = new AuthenticationException("User locked");
                        $token->setUser($user);
                        $exception->setToken($token);
                        return $authenticationFailureHandler->onAuthenticationFailure($request, $exception);
                    }
                }
                $jwt = $JWTManager->create($user);
                return $authenticationSuccessHandler->handleAuthenticationSuccess($user, $jwt);
            }

            $token->setUser($user);
        }
        $exception = new AuthenticationException("Invalid credential");
        $exception->setToken($token);
        return $authenticationFailureHandler->onAuthenticationFailure($request, $exception);
    }

}