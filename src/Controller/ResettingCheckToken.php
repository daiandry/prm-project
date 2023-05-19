<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 03/11/2020
 * Time: 11:56
 */

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ResettingCheckToken
{
    private $em;
    private const TOKEN_TTL = 3600;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __invoke(Request $request)
    {
        $token = $request->get('token');
        $response = new JsonResponse();
        $user = $this->em->getRepository(User::class)->findOneBy(['confirmationToken' => $token]);
        if ($user instanceof User
            && $user->isPasswordRequestNonExpired(self::TOKEN_TTL)) {
            return $response->setData(['code' => 200, 'message' => 'Token ok']);
        }

        return $response->setData(['code' => 404, 'message' => 'Token invalid']);

    }
}