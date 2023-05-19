<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 27/11/2020
 * Time: 10:12
 */

namespace App\Controller;


use App\Repository\PrmProjetRepository;
use App\Service\KpiProjetService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class KpiProjet
 * @package App\Controller
 */
class KpiProjet
{
    /**
     * @param Request $request
     * @param KpiProjetService $kpiProjetService
     * @return array
     */
    public function __invoke(Request $request, KpiProjetService $kpiProjetService)
    {
        $queryParams = explode("&", $request->getQueryString());
        $bodyQuery = new \stdClass();

        foreach ($queryParams as $query) {
            $query = explode("=", $query);
            ${$query[0]} = $query[1];
            $bodyQuery->{$query[0]} = $query[1];
        }
        $kpiProjet = $kpiProjetService->getKpiProjet($total, $restant, $encours, $realise, $retard,$surcout,$montant,$service,$kpiExist, $bodyQuery);

        return array('code' => 200, 'message' => 'ok', 'data' => $kpiProjet);
    }
}