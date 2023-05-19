<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 11/11/2020
 * Time: 14:15
 */

namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DisableUser
{
    private $em;
    private $passwordEncoder;
    private $user;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param User $data
     * @return JsonResponse
     */
    public function __invoke(User $data, Request $request)
    {
        $response = new JsonResponse();
        $listUsers = $request->get("users");
        $responseOk = $response->setData(['code' => 200, 'message' => 'Ok']);
        if ($listUsers) {
            foreach ($listUsers as $id) {
                $user = $this->em->getRepository(User::class)->find($id);
                if ($user) {
                    $user->setUserStatus($data->getUserStatus());
                    $this->em->flush();
                }
            }

            return $responseOk;

        } else {
            return $response->setStatusCode(Response::HTTP_NOT_FOUND)->setData(['code'=>"404", "message" => "Utilisateur introuvable"]);
        }

        return $response->setStatusCode(Response::HTTP_BAD_REQUEST)->setData(['code'=>"400", "message" => "Utilisateur introuvable"]);

    }
}