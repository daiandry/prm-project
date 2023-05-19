<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 17/11/2020
 * Time: 12:03
 */

namespace App\Controller;


use App\Entity\PrmProjet;
use App\Entity\PrmTaches;
use App\Repository\PrmTachesRepository;
use Symfony\Component\HttpFoundation\Request;

class ListTachesProjet
{

    public function __invoke(Request $request, PrmProjet $data, PrmTachesRepository $tachesRepository)
    {
        $page = (int)$request->query->get('page', 1);
        $itemsPerPage = (int)$request->query->get('itemsPerPage', 10);
        $taches = $tachesRepository->getTachesByProjet($page, $itemsPerPage, $data);
        //$taches = $this->encodeImage($data->getQuery()->getResult());
        return array("message" => "ok", "code" => 200, "data" => $taches, "totalItem" => $taches->getTotalItems());
    }

    /**
     * @param $datas
     */
    private function encodeImage($datas)
    {
        $taches = [];
        foreach ($datas as $data) {
            $photo = $data->getPhotos();
            if ($photo) {
                $file = @file_get_contents($photo->getChemin());
                $base64 = base64_encode($file);
                $photo->setChemin($base64);
            }
            $taches[] = $data;
        }
        return $taches;
    }

}