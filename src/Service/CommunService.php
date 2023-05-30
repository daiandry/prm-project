<?php
/**
 * Created by PhpStorm.
 * User: daandry
 * Date: 25/08/2020
 * Time: 13:26
 */

namespace App\Service;


use App\Entity\PrmHistoriqueAvancement;
use App\Entity\PrmProjet;
use App\Entity\PrmTaches;
use App\Entity\TraceLog;
use App\Entity\User;
use App\Utils\ConstantSrv;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommunService
{
    protected $entityManager;
    protected $trans;

    /**
     * CommunService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        $this->entityManager = $entityManager;
        $this->trans = $translator;
    }

    /**
     * @return \DateTime|false
     */
    public static function dateNow()
    {
        date_default_timezone_set('UTC');
        $date = new \DateTime();
        $date = date_modify($date, "+3 hour");
        return $date;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function getHistoryProject(ParamFetcher $paramFetcher)
    {
        try {
            $response = new JsonResponse();
            $idProjet = $paramFetcher->get('projet_id');
            $repProjet = $this->entityManager->getRepository(prmProjet::class);
            $oProject = $repProjet->findOneBy(array('id' => $idProjet));
            if ($oProject instanceof prmProjet) {
                $rep = $this->entityManager->getRepository(TraceLog::class);
                $nameClass = $this->entityManager->getClassMetadata(get_class($oProject))->getName();
                $nameClass = join('', array_slice(explode('\\', $nameClass), -1));
                $oActLog = $rep->findBy(array('classeName' => $nameClass, 'ressourceId' => $idProjet));
                $context = SerializationContext::create()->setGroups('log:read');
                $context->setSerializeNull(true);
                $serializer = SerializerBuilder::create()->build();
                $results = json_decode($serializer->serialize($oActLog, 'json', $context));
                $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $results);
            } else {
                $data = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('project_not_found'));
            }
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param PrmProjet $oProjet
     */
    public function setHistoriqueAvancementProjet(PrmProjet $oProjet)
    {
        try {
            $repTache = $this->entityManager->getRepository(PrmTaches::class);
            $sommeTacheReel = $repTache->historiqueAvancementProjet($oProjet);
            $date = ($oProjet->getDateModify() != null) ? $oProjet->getDateModify() : $oProjet->getDateCreate();
            $avancementPhysique = $oProjet->getAvancement();
            $avancementFinancier = ($oProjet->getRfAutorisationEngagement() != 0 && $oProjet->getRfAutorisationEngagement() != null) ? $sommeTacheReel * 100 / $oProjet->getRfAutorisationEngagement() : 0;
            $budgetPrevu = $oProjet->getRfAutorisationEngagement();
            $libStatus = ($oProjet->getStatut() !== null) ? $oProjet->getStatut()->getLibelle() : "";
            $nomAuteur = ($oProjet->getCreatedBy() !== null) ? ($oProjet->getCreatedBy()->getNom() != "" ? $oProjet->getCreatedBy()->getNom() : $oProjet->getCreatedBy()->getEmail()) : "";
            $history = new PrmHistoriqueAvancement();
            $history->setDate($date);
            $history->setAvancementPhysique($avancementPhysique);
            $history->setAvancementFinanciere($avancementFinancier);
            $history->setBudgetPrevu($budgetPrevu);
            $history->setStatut($oProjet->getStatut());
            $history->setAuteur($oProjet->getCreatedBy());
            $history->setProjet($oProjet);
            $this->entityManager->persist($history);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function getListHistoriqueAvancement(ParamFetcher $paramFetcher)
    {
        $response = new JsonResponse();
        $result = ['list' => [], 'total' => 0];
        $idProjet = $paramFetcher->get('projet_id');
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('itemsPerPage');
        $rep = $this->entityManager->getRepository(PrmHistoriqueAvancement::class);
        $allHistory = $rep->listHistoriqueAvancementProjet($idProjet, $page, $limit);
        if (isset($allHistory['list']) && isset($allHistory['list']) && !empty($allHistory['list'])) {
            $result['total'] = $allHistory['total'];
            foreach ($allHistory['list'] as $oHistory) {
                if ($oHistory instanceof PrmHistoriqueAvancement) {
                    array_push($result['list'], [
                        'date' => $oHistory->getDate(),
                        'avancementPhysique' => $oHistory->getAvancementPhysique(),
                        'avancementFinancier' => $oHistory->getAvancementFinanciere(),
                        'budgetPrevu' => $oHistory->getBudgetPrevu(),
                        'status' => $oHistory->getStatut() !== null ? $oHistory->getStatut()->getLibelle() : "",
                        'auteur' => $oHistory->getAuteur() !== null ? ($oHistory->getAuteur()->getNom() != "" ? $oHistory->getAuteur()->getNom() : $oHistory->getAuteur()->getEmail()) : ""
                    ]);
                }
            }
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
        $response->setData($data);
        return $response;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function getMontantByProjetId(ParamFetcher $paramFetcher)
    {
        $response = new JsonResponse();
        $idProjet = $paramFetcher->get('projet_id');
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $result = ['rf_autorisation_engagement' => 0, 'somme_montant_decaisse_mandate' => 0];
        $projet = $rep->findOneBy(array('id' => $idProjet));
        if ($projet instanceof PrmProjet) {
            $montantDepenseMandate = $projet->getRfMontantDepensesDecaissessMandate();
            $montantDepenseLiquide = $projet->getRfMontantDepensesDecaissessLiquide();
            $tachesProjet = $projet->getTaches();
            foreach ($tachesProjet as $tache) {
                if ($typeTache = $tache->getTypeTache()) {
                    if ($typeTache->getId() == ConstantSrv::MONTANT_DECAISSE_MANDATE) {
                        $montantDepenseMandate += (float)$tache->getValeurReel();
                    } elseif ($typeTache->getId() == ConstantSrv::MONTANT_DECAISSE_LIQUIDE) {
                        $montantDepenseLiquide += (float)$tache->getValeurReel();
                    }
                }
            }
            $result['rf_autorisation_engagement'] = $projet->getRfAutorisationEngagement();
            $result['somme_montant_decaisse_mandate'] = $montantDepenseMandate + $montantDepenseLiquide;
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
        $response->setData($data);
        return $response;
    }
}