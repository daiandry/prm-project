<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 05/11/2020
 * Time: 13:20
 */

namespace App\Controller;

use App\Entity\PrmHistoriqueAvancement;
use App\Entity\PrmProjet;
use App\Entity\User;
use App\Repository\PrmTachesRepository;
use App\Repository\UserRepository;
use App\Service\CommunService;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 * Class ListUsers
 * @package App\Controller
 */
class HistoriqueAvancementProjet
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return array
     */
    public function __invoke(PrmProjet $data, PrmTachesRepository $prmTachesRepository)
    {
        $sommeTacheReel = $prmTachesRepository->historiqueAvancementProjet($data);

        return array("message" => "ok", "code" => 200, "data" => ['historiques' =>
                [
                    'date' => $data->getDateModify() != null?$data->getDateModify():$data->getDateCreate(),
                    'avancementPhysique' => $data->getAvancement(),
                    'avancementFinancier' => $data->getRfAutorisationEngagement() != 0 && $data->getRfAutorisationEngagement() != null ?$sommeTacheReel*100/$data->getRfAutorisationEngagement():0,
                    'budgetPrevu' => $data->getRfAutorisationEngagement(),
                    'status' => $data->getStatut() !== null ?$data->getStatut()->getLibelle():"",
                    'auteur' => $data->getCreatedBy() !== null ?($data->getCreatedBy()->getNom()!= ""?$data->getCreatedBy()->getNom():$data->getCreatedBy()->getEmail()):""
                ]
            ]
        );
    }

    /**
     * Liste la liste des projets parents
     * @Rest\Post("/api/listHistoriqueAvancement", name ="api_get_list_historique_avancement")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @Rest\RequestParam(name="page", nullable=false)
     * @Rest\RequestParam(name="itemsPerPage", nullable=false)
     * @return JsonResponse
     */
    public function listHistoriqueAvancement(ParamFetcher $paramFetcher,CommunService $commun)
    {
        $response = $commun->getListHistoriqueAvancement($paramFetcher);
        return $response;
    }
}