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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class ResetPassword
 * @package App\Controller
 */
class ResetPassword
{
    private $em;
    private $passwordEncoder;
    private $user;
    private const TOKEN_TTL = 3600;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, Security $security)
    {
        $this->em = $entityManager;
        $this->passwordEncoder = $userPasswordEncoder;
        $this->user = $security->getUser();
    }

    /**
     * @param User $data
     */
    public function __invoke(User $data, Request $request)
    {
        $token = $request->get('token');
        if ($token !== null) {
            $user = $this->em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);
        }

        if (!$user) {
            return new JsonResponse(['message' => 'User not found', 'code' => Response::HTTP_NOT_FOUND]);
        }

        if (!$user->isPasswordRequestNonExpired(self::TOKEN_TTL)) {
            return new JsonResponse(['message' => 'Token Invalid', 'code' => 498]);
        }

        if ($data->getPlainPassword()) {
            $user->setPassword($data->getPlainPassword());
            $user->setConfirmationToken(null);
            $user->setFirstLogin(false);
            $user->eraseCredentials();
            $this->em->flush();
            return new JsonResponse(['message' => 'resetting ok', 'code' => Response::HTTP_OK]);
        }

        throw new NotFoundHttpException('Resource not found');

    }
}