<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 03/11/2020
 * Time: 10:58
 */

namespace App\Controller;


use App\Entity\User;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SendMailUser
{
    private $em;
    private $mailer;
    private $tokenGenerator;
    private const RETRY_TTL = 3600;
    public function __construct(EntityManagerInterface $entityManager, Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function __invoke(User $data)
    {
        $email = $data->getEmail();
        $response = new JsonResponse();
        if ($user = $this->em->getRepository(User::class)->findOneBy(['email' => $email])) {

            if ($user->isPasswordRequestNonExpired(self::RETRY_TTL)) {
                return $response->setData(['code' => 401, 'message' => "Envoyer un email après 1 heure"]);
            }
            if ($user->getLocked()) {
                return $response->setData(['code' => 423, 'message' => "Compte bloqué"]);
            }
            if ($user->getFirstLogin()) {
                return $response->setData(['code' => 400, 'message' => "First connexion"]);
            }
            $token = $this->tokenGenerator->generateToken();
            $user->setConfirmationToken($token);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->em->persist($user);
            $this->em->flush();
            $pathConfirmation = $_ENV['HOST_FRONT_CONFIRMATION']."/$token";
            $tokenLifetime = ceil(self::RETRY_TTL / 3600);
            $this->mailer->sendMail($email, "Réinitialisation mot de passe", $pathConfirmation, $tokenLifetime);
            return $response->setData(['message'=>'Ok','code' => 200 ]);
        }

        return $response->setData(['code' => 404, 'message' => "Email introuvable $email"]);

    }
}