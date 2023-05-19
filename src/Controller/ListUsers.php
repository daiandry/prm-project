<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 05/11/2020
 * Time: 13:20
 */

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FluxService;
use App\Service\UserService;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Class ListUsers
 * @package App\Controller
 */
class ListUsers
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return array
     */
    public function __invoke(Request $request, UserRepository $userRepository)
    {
        $page = (int)$request->query->get('page', 1);
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 10);
        $enabled = $request->get('enabled');
        $administration = (int)$request->get('administration');
        $email = $request->get('email');
        $nom = $request->get('nom');
        $region = $request->get('region');
        $data = $userRepository->getBooksByFavoriteAuthor($page, $itemsPerPage, $nom, $email, $administration, $enabled, $region);
        $users = $this->encodeImage($data->getQuery()->getResult());
        return array("message" => "ok", "code" => 200, "data" => $users, "totalItem" => $data->getTotalItems());
    }

    /**
     * @param $datas
     */
    private function encodeImage($datas)
    {
        $users = [];
        foreach ($datas as $data) {
            $photo = $data->getPhoto();
            if ($photo) {
                $file = @file_get_contents($photo->getChemin());
                $base64 = base64_encode($file);
                $photo->setChemin($base64);
            }
            $users[] = $data;
        }

        return $users;
    }

    /**
     * Liste nombre total des utilisateurs
     * @Rest\Get("/api/getCountUser", name ="api_get_count_user")
     * @return JsonResponse
     */
    public function getCountUser(UserService $userService)
    {
        $response = $userService->getNombreUtilisateur();
        return $response;
    }

    /**
     * Liste les utilisateurs par profil
     * @Rest\Post("/api/getUserByProfil", name ="api_get_user_by_profil")
     * @Rest\RequestParam(name="profil_id", nullable=false)
     * @return JsonResponse
     */
    public function getUserGroupByProfil(ParamFetcher $paramFetcher, UserService $userService)
    {
        $response = $userService->getNombreUtilisateurParProfil($paramFetcher);
        return $response;
    }
}