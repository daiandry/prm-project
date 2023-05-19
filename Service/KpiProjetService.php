<?php
/**
 * Created by PhpStorm.
 * User: nambinina2
 * Date: 27/11/2020
 * Time: 10:31
 */

namespace App\Service;


use App\Entity\User;
use App\Model\ProjetModel;
use App\Repository\PrmProjetRepository;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Utils\ConstantSrv;

class KpiProjetService
{
    private $projetRep;
    private $container;

    public function __construct(PrmProjetRepository $projetRepository, ContainerInterface $container)
    {
        $this->projetRep = $projetRepository;
        $this->container = $container;
    }

    /**
     * @param bool $total
     * @param bool $restant
     * @param bool $afaire
     * @param bool $realised
     * @return mixed
     */
    public function getKpiProjet($total = false, $restant = false, $enCours = false, $realised = false, $enRetard = false, $surcout = false, $montant_engage_decaisse = false, $ordre_service = false, $kpiExist = true, $bodyQuery)
    {
        $kpiNbrTotal = null;
        $kpiNbrAfaire = null;
        $kpiEnCours = null;
        $kpiNbrRealised = null;
        $kpiNbrRetard = null;
        $kpiSurcout = null;
        $kpiMontantEngageDecaisse = null;
        $kpiOrdreService = null;
        if ($kpiExist == 1) {
            if ($total) {
                $kpiNbrTotal = $this->projetRep->getTotalProjet($total, false, false, false, false, false, false, $bodyQuery, $this->container);
            }

            if ($restant) {
                $kpiNbrAfaire = $this->projetRep->getTotalProjet(false, $restant, false, false, false, false, false, $bodyQuery, $this->container);
            }

            if ($enCours) {
                $kpiEnCours = $this->projetRep->getTotalProjet(false, false, $enCours, false, false, false, false, $bodyQuery, $this->container);
            }

            if ($realised) {
                $kpiNbrRealised = $this->projetRep->getTotalProjet(false, false, false, $realised, false, false, false, $bodyQuery, $this->container);
            }

            if ($enRetard) {
                $kpiNbrRetard = $this->projetRep->getTotalProjet(false, false, false, false, $enRetard, false, false, $bodyQuery, $this->container);
            }

            if ($surcout) {
                $kpiSurcout = $this->projetRep->getTotalProjet(false, false, false, false, false, $surcout, false, $bodyQuery, $this->container);
            }

            if ($montant_engage_decaisse) {
                $kpiMontantEngageDecaisse = true;
            }

            if ($ordre_service) {
                $kpiOrdreService = $this->projetRep->getTotalProjet(false, false, false, false, $enRetard, false, $ordre_service, $bodyQuery, $this->container);
            }

            return $this->buildKpiProjet($kpiNbrTotal, $kpiNbrAfaire, $kpiEnCours, $kpiNbrRealised, $kpiNbrRetard, $kpiSurcout, $kpiMontantEngageDecaisse, $kpiOrdreService, $bodyQuery);
        } else {
            $data = $this->projetRep->getMinInfoProjet($bodyQuery);
            return $data;
        }
    }

    /**
     * @param null $kpiTotal
     * @param null $kpiRestant
     * @param null $kpiAfaire
     * @param null $kpiRealised
     */
    public function buildKpiProjet($kpiTotal = null, $kpiNbrAfaire = null, $kpiEnCours = null, $kpiRealised = null, $kpiNbrRetard, $kpiSurcout = null, $kpiMontantEngageDecaisse = null, $kpiOrdreService = null, $bodyQuery)
    {
        $allKpi = [];

        if ($kpiTotal) {
            $obKpiTotal = new ProjetModel();
            $obKpiTotal->setLibelle($this->container->getParameter('kpi_projets')['total'][0]);
            $obKpiTotal->setTotal($kpiTotal[0]['total']);
            $obKpiTotal->setType($this->container->getParameter('kpi_projets')['total'][1]);
            $allKpi[] = $obKpiTotal;
            $obKpiTotal = null;
        }

        if ($kpiNbrAfaire) {
            $obKpiAfaire = new ProjetModel();
            $obKpiAfaire->setLibelle($this->container->getParameter('kpi_projets')['a_faire'][0]);
            $obKpiAfaire->setTotal($kpiNbrAfaire[0]['total']);
            $obKpiAfaire->setPercent((isset($kpiTotal[0]['total']) && ($kpiTotal[0]['total'] !== 0)) ? $kpiNbrAfaire[0]['total'] * 100 / $kpiTotal[0]['total'] : 0);
            $obKpiAfaire->setType($this->container->getParameter('kpi_projets')['a_faire'][1]);

            $allKpi[] = $obKpiAfaire;
            $obKpiAfaire = null;
        }

        if ($kpiEnCours) {
            $obKpiEnCours = new ProjetModel();
            $obKpiEnCours->setLibelle($this->container->getParameter('kpi_projets')['en_cours'][0]);
            $obKpiEnCours->setTotal($kpiEnCours[0]['total']);
            $obKpiEnCours->setPercent((isset($kpiTotal[0]['total']) && ($kpiTotal[0]['total'] !== 0) && ($kpiTotal[0]['total'] != null)) ? $kpiEnCours[0]['total'] * 100 / $kpiTotal[0]['total'] : 0);
            $obKpiEnCours->setType($this->container->getParameter('kpi_projets')['en_cours'][1]);
            $allKpi[] = $obKpiEnCours;
            $obKpiEnCours = null;
        }

        if ($kpiRealised) {
            $obKpiRealised = new ProjetModel();
            $obKpiRealised->setLibelle($this->container->getParameter('kpi_projets')['realise'][0]);
            $obKpiRealised->setType($this->container->getParameter('kpi_projets')['realise'][1]);
            $obKpiRealised->setTotal($kpiRealised[0]['total']);
            $obKpiRealised->setPercent((isset($kpiTotal[0]['total']) && ($kpiTotal[0]['total'] !== 0) && ($kpiTotal[0]['total'] != null)) ? $kpiRealised[0]['total'] * 100 / $kpiTotal[0]['total'] : 0);
            $allKpi[] = $obKpiRealised;
            $obKpiRealised = null;
        }

        if ($kpiNbrRetard) {
            $obKpiEnRetard = new ProjetModel();
            $obKpiEnRetard->setLibelle($this->container->getParameter('kpi_projets')['en_retard'][0]);
            $obKpiEnRetard->setType($this->container->getParameter('kpi_projets')['en_retard'][1]);
            $obKpiEnRetard->setTotal($kpiNbrRetard[0]['total']);
            $obKpiEnRetard->setPercent((isset($kpiTotal[0]['total']) && ($kpiTotal[0]['total'] !== 0) && ($kpiTotal[0]['total'] != null)) ? $kpiNbrRetard[0]['total'] * 100 / $kpiTotal[0]['total'] : 0);
            $allKpi[] = $obKpiEnRetard;
            $obKpiRealised = null;
        }


        if ($kpiSurcout) {
            $obKpiSurcout = new ProjetModel();
            $obKpiSurcout->setLibelle($this->container->getParameter('kpi_projets')['surcout'][0]);
            $obKpiSurcout->setTotal($kpiSurcout[0]['total']);
            $obKpiSurcout->setPercent((isset($kpiTotal[0]['total']) && ($kpiTotal[0]['total'] !== 0)) ? $kpiSurcout[0]['total'] * 100 / $kpiTotal[0]['total'] : 0);
            $obKpiSurcout->setType($this->container->getParameter('kpi_projets')['surcout'][1]);
            $allKpi[] = $obKpiSurcout;
            $obKpiSurcout = null;
        }

        if ($kpiMontantEngageDecaisse) {
            $obKpiMontant = new ProjetModel();
            $engage = $this->projetRep->returnMontantEngage($kpiMontantEngageDecaisse, $bodyQuery);
            $decaisse = $this->projetRep->returnMontantDecaisser($kpiMontantEngageDecaisse, $bodyQuery);
            $value = (($engage[0]['somme']) && is_numeric($engage[0]['somme']) && ($decaisse[0]['somme']) && is_numeric($decaisse[0]['somme'])) ? ($engage[0]['somme'] / $decaisse[0]['somme'] * 100) : 0;
            $Montant = $this->projetRep->countProjetMontant($kpiMontantEngageDecaisse, $bodyQuery);
            $obKpiMontant->setLibelle($this->container->getParameter('kpi_projets')['montant'][0]);
            $obKpiMontant->setType($this->container->getParameter('kpi_projets')['montant'][1]);
            $obKpiMontant->setTotal((isset($Montant['totalMontant'])) ? $Montant['totalMontant'] : 0);
            $obKpiMontant->setPercent((isset($Montant['totalMontant']) && is_numeric($Montant['totalMontant']) && ($kpiTotal[0]['total'] !== 0) && ($kpiTotal[0]['total'] != null)) ? $Montant['totalMontant'] / $kpiTotal[0]['total'] : 0);
            $allKpi[] = $obKpiMontant;
            $obKpiMontant = null;
        }

        if ($kpiOrdreService) {
            $obKpiOrdreService = new ProjetModel();
            $obKpiOrdreService->setLibelle($this->container->getParameter('kpi_projets')['service'][0]);
            $obKpiOrdreService->setType($this->container->getParameter('kpi_projets')['service'][1]);
            $obKpiOrdreService->setTotal($kpiOrdreService[0]['total']);
            $obKpiOrdreService->setPercent((isset($kpiTotal[0]['total']) && ($kpiTotal[0]['total'] !== 0) && ($kpiTotal[0]['total'] != null)) ? $kpiOrdreService[0]['total'] * 100 / $kpiTotal[0]['total'] : 0);
            $allKpi[] = $obKpiOrdreService;
            $obKpiOrdreService = null;
        }
        return $allKpi;
    }

    /**
     * @param $paramFetcher
     * @param User $user
     */
    public function getKpiOrMinInfoProjet(ParamFetcher $paramFetcher, User $user)
    {

        $data['total'] = $paramFetcher->get('total');
        $data['restant'] = $paramFetcher->get('restant');
        $data['encours'] = $paramFetcher->get('encours');
        $data['retard'] = $paramFetcher->get('retard');
        $data['realise'] = $paramFetcher->get('realise');
        $data['surcout'] = $paramFetcher->get('surcout');
        $data['montant'] = $paramFetcher->get('montant');
        $data['service'] = $paramFetcher->get('service');
        $data['kpiExist'] = $paramFetcher->get('kpiExist');
        // ------------ Filtre ------------
        $data['nomProjet'] = $paramFetcher->get('nomProjet');
        $data['typeAdmin'] = $paramFetcher->get('typeAdmin');
        $data['administration'] = $paramFetcher->get('administration');
        $data['statut'] = $paramFetcher->get('statut');
        $data['dateFinPrevdebut'] = $paramFetcher->get('dateFinPrevdebut');
        $data['dateFinPrevfin'] = $paramFetcher->get('dateFinPrevfin');
        $data['secteur'] = $paramFetcher->get('secteur');
        $data['engagement'] = $paramFetcher->get('engagement');
        $data['region'] = $paramFetcher->get('region');
        $data['enRetard'] = $paramFetcher->get('enRetard');
        $data['inaugurable'] = $paramFetcher->get('inaugurable');
        $data['montantEngage'] = $paramFetcher->get('montantEngage');
        $data['montantDecaisse'] = $paramFetcher->get('montantDecaisse');
        $data['user'] = $user;
        $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
        $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;

        $total_projet_en_cours = 1;

        if ($data['kpiExist'] == 1) {
            $allKpi = [];
            if ($data['total']) {
                $kpiTotal = $this->projetRep->getNombreTotal($data, $administration, $region);
                $kpiTotal = (isset($kpiTotal[0]['total'])) ? $kpiTotal[0]['total'] : 0;
                $obKpiTotal = new ProjetModel();
                $obKpiTotal->setLibelle($this->container->getParameter('kpi_projets')['total'][0]);
                $obKpiTotal->setTotal($kpiTotal);
                $obKpiTotal->setType($this->container->getParameter('kpi_projets')['total'][1]);
                $allKpi[] = $obKpiTotal;
                $obKpiTotal = null;
            }
            if ($data['restant']) {
                $obKpiAfaire = new ProjetModel();
                $nbr = $this->projetRep->getNombreRestant($data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $obKpiAfaire->setLibelle($this->container->getParameter('kpi_projets')['a_faire'][0]);
                $obKpiAfaire->setTotal($nbr);
                $obKpiAfaire->setPercent((isset($kpiTotal) && ($kpiTotal !== 0)) ? $nbr * 100 / $kpiTotal : 0);
                $obKpiAfaire->setType($this->container->getParameter('kpi_projets')['a_faire'][1]);

                $allKpi[] = $obKpiAfaire;
                $obKpiAfaire = null;
            }

            if ($data['encours']) {
                $obKpiEnCours = new ProjetModel();
                $nbr = $this->projetRep->getNombreEncours($data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $total_projet_en_cours = $nbr;
                $obKpiEnCours->setLibelle($this->container->getParameter('kpi_projets')['en_cours'][0]);
                $obKpiEnCours->setTotal($nbr);
                $obKpiEnCours->setPercent((isset($kpiTotal) && ($kpiTotal !== 0) && ($kpiTotal != null)) ? $nbr * 100 / $kpiTotal : 0);
                $obKpiEnCours->setType($this->container->getParameter('kpi_projets')['en_cours'][1]);
                $allKpi[] = $obKpiEnCours;
                $obKpiEnCours = null;
            }

            if ($data['realise']) {
                $obKpiRealised = new ProjetModel();
                $nbr = $this->projetRep->getNombreTerminer($data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $obKpiRealised->setLibelle($this->container->getParameter('kpi_projets')['realise'][0]);
                $obKpiRealised->setType($this->container->getParameter('kpi_projets')['realise'][1]);
                $obKpiRealised->setTotal($nbr);
                $obKpiRealised->setPercent((isset($kpiTotal) && ($kpiTotal !== 0) && ($kpiTotal != null)) ? $nbr * 100 / $kpiTotal : 0);
                $allKpi[] = $obKpiRealised;
                $obKpiRealised = null;
            }

            if ($data['retard']) {
                $obKpiEnRetard = new ProjetModel();
                $nbr = $this->projetRep->getNombreRetard($data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $obKpiEnRetard->setLibelle($this->container->getParameter('kpi_projets')['en_retard'][0]);
                $obKpiEnRetard->setType($this->container->getParameter('kpi_projets')['en_retard'][1]);
                $obKpiEnRetard->setTotal($nbr);
                $obKpiEnRetard->setPercent((isset($kpiTotal) && ($kpiTotal !== 0) && ($kpiTotal != null)) ? $nbr * 100 / $kpiTotal : 0);
                $allKpi[] = $obKpiEnRetard;
                $obKpiRealised = null;
            }


            if ($data['surcout']) {
                $obKpiSurcout = new ProjetModel();
                $nbr = $this->projetRep->getNombreSurcout($data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $obKpiSurcout->setLibelle($this->container->getParameter('kpi_projets')['surcout'][0]);
                $obKpiSurcout->setTotal($nbr);
                $obKpiSurcout->setPercent((isset($kpiTotal) && ($kpiTotal !== 0)) ? $nbr * 100 / $kpiTotal : 0);
                $obKpiSurcout->setType($this->container->getParameter('kpi_projets')['surcout'][1]);
                $allKpi[] = $obKpiSurcout;
                $obKpiSurcout = null;
            }

            if ($data['montant']) {
                $obKpiMontant = new ProjetModel();
                //$nbr = $this->projetRep->getNombreMontantV2($data, $administration, $region);
                $engage = $this->projetRep->returnMontantEngageV2(true, $data, $administration, $region);
                $decaisse = $this->projetRep->returnMontantDecaisserV2(true, $data, $administration, $region);
                $engage = (!empty($engage) && isset($engage[0]['somme'])) ? $engage[0]['somme'] : 0;
                $decaisse = (!empty($decaisse) && isset($decaisse[0]['somme'])) ? $decaisse[0]['somme'] : 0;
                $value = (($engage) && is_numeric($engage) && ($decaisse) && is_numeric($decaisse) && $decaisse != 0) ? ($engage / $decaisse * 100) : 0;
                $nbr = $this->projetRep->countProjetMontantV2(true, $data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $obKpiMontant->setLibelle($this->container->getParameter('kpi_projets')['montant'][0]);
                $obKpiMontant->setType($this->container->getParameter('kpi_projets')['montant'][1]);
                $obKpiMontant->setTotal((isset($nbr)) ? $nbr : 0);
                $obKpiMontant->setPercent((isset($nbr) && is_numeric($nbr) && ($kpiTotal !== 0) && ($kpiTotal != null)) ? $nbr / $kpiTotal : 0);
                $allKpi[] = $obKpiMontant;
                $obKpiMontant = null;
            }

            if ($data['montantEngage']) {
                $obKpiMontantEngage = new ProjetModel();
                //$nbr = $this->projetRep->getNombreMontantV2($data, $administration, $region);
                $engage = $this->projetRep->returnMontantEngageV2(true, $data, $administration, $region);
                $engage = (!empty($engage) && isset($engage[0]['somme'])) ? $engage[0]['somme'] : 0;

                $obKpiMontantEngage->setLibelle($this->container->getParameter('kpi_projets')['montant_engage'][0]);
                $obKpiMontantEngage->setType($this->container->getParameter('kpi_projets')['montant_engage'][1]);
                $obKpiMontantEngage->setTotal($engage);
                $obKpiMontantEngage->setPercent(0);
                $allKpi[] = $obKpiMontantEngage;
                $obKpiMontantEngage = null;
            }

            if ($data['montantDecaisse']) {
                $obKpiMontantDecaisse = new ProjetModel();
                //$nbr = $this->projetRep->getNombreMontantV2($data, $administration, $region);
                $decaisse = $this->projetRep->returnMontantDecaisserV2(true, $data, $administration, $region);
                $decaisse = (!empty($decaisse) && isset($decaisse[0]['somme']) && $decaisse[0]['somme'] != NULL) ? $decaisse[0]['somme'] : 0;

                $obKpiMontantDecaisse->setLibelle($this->container->getParameter('kpi_projets')['montant_decaisse'][0]);
                $obKpiMontantDecaisse->setType($this->container->getParameter('kpi_projets')['montant_decaisse'][1]);
                $obKpiMontantDecaisse->setTotal($decaisse);
                $obKpiMontantDecaisse->setPercent(0);
                $allKpi[] = $obKpiMontantDecaisse;
                $obKpiMontantDecaisse = null;
            }

            if ($data['service']) {
                $data['statut'] = ConstantSrv::STATUT_PROJET_EN_COURS;
                if ($total_projet_en_cours == 0) {
                    $total_projet_en_cours = 1;
                }
                $nbr = $this->projetRep->getNombreService($data, $administration, $region);
                $nbr = (isset($nbr[0]['total'])) ? $nbr[0]['total'] : 0;
                $obKpiOrdreService = new ProjetModel();
                $obKpiOrdreService->setLibelle($this->container->getParameter('kpi_projets')['service'][0]);
                $obKpiOrdreService->setType($this->container->getParameter('kpi_projets')['service'][1]);
                $obKpiOrdreService->setTotal($nbr);
                $obKpiOrdreService->setPercent((isset($kpiTotal) && ($kpiTotal !== 0) && ($kpiTotal != null)) ? $nbr * 100 / $total_projet_en_cours : 0);
                $allKpi[] = $obKpiOrdreService;
                $obKpiOrdreService = null;
            }
            $data = $allKpi;
        } else {
            $data = $this->projetRep->getMinInfoProjetV2($data, $administration, $region);
        }
        return array('code' => 200, 'message' => 'ok', 'data' => $data);
    }
}