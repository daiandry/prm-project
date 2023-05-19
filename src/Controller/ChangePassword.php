<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 29/10/2020
 * Time: 11:49
 */

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;

/**
 * Class ResetPassword
 * @package App\Controller
 */
class ChangePassword
{
    private $em;
    private $user;

    private const TOKEN_TTL = 3600;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->em = $entityManager;
        $this->user = $security->getUser();
    }

    /**
     * @param User $data
     */
    public function __invoke(User $data)
    {
        $token = $data->getConfirmationToken();
        $response = new JsonResponse();

        if (!$token) {
            return $response->setData(['code'=>401, 'message' => 'Confirmation token required']);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);

        if (!($user instanceof User)) {
            return $response->setData(['code' => 403, 'message' => 'User introuvable']);
        }
        if (!$user->isPasswordRequestNonExpired(self::TOKEN_TTL)) {
            return $response->setData(['message' => 'Token Invalid', 'code' => 498]);
        }
        if ($data->getPlainPassword()) {
            $user->setPassword(
                $data->getPlainPassword()
            );
            $user->setConfirmationToken(null);
            $user->eraseCredentials();
            $this->em->persist($user);
            $this->em->flush();
            return $response->setData(['code' => 200, 'message' => 'OK']);
        }

        return $response->setData(['code' => 401, 'message' => 'Mot de passe invalide']);

    }
}