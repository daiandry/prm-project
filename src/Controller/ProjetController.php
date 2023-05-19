<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\Controller;

use App\Service\CommunService;
use App\Service\FileUpload;
use App\Service\FluxService;
use App\Service\KpiProjetService;
use App\Service\ProjetService;
use App\Service\ZoneService;
use App\Utils\ConstantSrv;
use Doctrine\DBAL\Driver\PDO\Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjetController extends AbstractController
{
    /**
     * @Rest\Post("/api/createProjet", name ="api_create_project")
     * @Rest\RequestParam(name="nom", nullable=false)
     * @Rest\RequestParam(name="conv_cl", nullable=false)
     * @Rest\RequestParam(name="projet_parent_id", nullable=true)
     * @Rest\RequestParam(name="coordonnee_gps", nullable=false)
     * @Rest\RequestParam(name="localite_emplacement", nullable=false)
     * @Rest\RequestParam(name="engagement", nullable=false)
     * @Rest\RequestParam(name="categorie", nullable=true)
     * @Rest\RequestParam(name="soa_code", nullable=false)
     * @Rest\RequestParam(name="pcop_compte", nullable=false)
     * @Rest\RequestParam(name="description", nullable=true)
     * @Rest\RequestParam(name="prommesse_presidentielle", nullable=true)
     * @Rest\RequestParam(name="projet_inaugurable", nullable=true)
     * @Rest\RequestParam(name="priorite", nullable=true)
     * @Rest\RequestParam(name="date_inauguration", nullable=true)
     * @Rest\RequestParam(name="secteur", nullable=false)
     * @Rest\RequestParam(name="type", nullable=false)
     * @Rest\RequestParam(name="statut", nullable=true)
     * @Rest\RequestParam(name="situation_actuelle_travaux", nullable=true)
     *
     * @Rest\RequestParam(name="pdm_date_debut_appel_offre", nullable=false)
     * @Rest\RequestParam(name="pdm_date_fin_offre", nullable=false)
     * @Rest\RequestParam(name="pdm_date_signature_contrat", nullable=true)
     * @Rest\RequestParam(name="pdm_titulaire_du_marche", nullable=true)
     * @Rest\RequestParam(name="pdm_designation", nullable=true)
     * @Rest\RequestParam(name="pdm_tiers_nif", nullable=true)
     * @Rest\RequestParam(name="pdm_date_lancement_os", nullable=true)
     * @Rest\RequestParam(name="pdm_date_lancement_travaux_prevu", nullable=true)
     * @Rest\RequestParam(name="pdm_date_lancement_travaux_reel", nullable=true)
     * @Rest\RequestParam(name="pdm_delai_execution_prevu", nullable=true)
     * @Rest\RequestParam(name="pdm_date_fin_prevu", nullable=true)
     *
     * @Rest\RequestParam(name="rf_date_signature_autorisation_engagement", nullable=true)
     * @Rest\RequestParam(name="rf_autorisation_engagement", nullable=true)
     * @Rest\RequestParam(name="rf_credit_payement_annee_en_cours", nullable=true)
     * @Rest\RequestParam(name="rf_montant_depenses_decaisees_mandate", nullable=true)
     * @Rest\RequestParam(name="rf_montant_depenses_decaisees_liquide", nullable=true)
     * @Rest\RequestParam(name="rf_exercice_budgetaire", nullable=true)
     * @Rest\RequestParam(name="situation_projet", nullable=false)
     * @Rest\RequestParam(name="avancement", nullable=true)
     * @Rest\RequestParam(name="observation", nullable=true)
     * @Rest\RequestParam(name="photos", nullable=true)
     * @Rest\RequestParam(name="document", nullable=true)
     *
     * @return JsonResponse
     */
    public function createProject(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = new JsonResponse();
        $retour = $porojetService->saveProject($paramFetcher, $this->getUser(), false);
        $response->setData($retour);
        return $response;
    }

    /**
     * @Rest\Post("/api/editProjet", name ="api_edit_project")
     * @Rest\RequestParam(name="id", nullable=false)
     * @Rest\RequestParam(name="nom", nullable=false)
     * @Rest\RequestParam(name="conv_cl", nullable=false)
     * @Rest\RequestParam(name="projet_parent_id", nullable=true)
     * @Rest\RequestParam(name="coordonnee_gps", nullable=false)
     * @Rest\RequestParam(name="localite_emplacement", nullable=false)
     * @Rest\RequestParam(name="engagement", nullable=false)
     * @Rest\RequestParam(name="categorie", nullable=true)
     * @Rest\RequestParam(name="soa_code", nullable=false)
     * @Rest\RequestParam(name="pcop_compte", nullable=false)
     * @Rest\RequestParam(name="description", nullable=true)
     * @Rest\RequestParam(name="prommesse_presidentielle", nullable=true)
     * @Rest\RequestParam(name="projet_inaugurable", nullable=true)
     * @Rest\RequestParam(name="priorite", nullable=true)
     * @Rest\RequestParam(name="date_inauguration", nullable=true)
     * @Rest\RequestParam(name="secteur", nullable=false)
     * @Rest\RequestParam(name="type", nullable=false)
     * @Rest\RequestParam(name="statut", nullable=true)
     * @Rest\RequestParam(name="situation_actuelle_travaux", nullable=true)
     *
     * @Rest\RequestParam(name="pdm_date_debut_appel_offre", nullable=false)
     * @Rest\RequestParam(name="pdm_date_fin_offre", nullable=false)
     * @Rest\RequestParam(name="pdm_date_signature_contrat", nullable=true)
     * @Rest\RequestParam(name="pdm_titulaire_du_marche", nullable=true)
     * @Rest\RequestParam(name="pdm_designation", nullable=true)
     * @Rest\RequestParam(name="pdm_tiers_nif", nullable=true)
     * @Rest\RequestParam(name="pdm_date_lancement_os", nullable=true)
     * @Rest\RequestParam(name="pdm_date_lancement_travaux_prevu", nullable=true)
     * @Rest\RequestParam(name="pdm_date_lancement_travaux_reel", nullable=true)
     * @Rest\RequestParam(name="pdm_delai_execution_prevu", nullable=true)
     * @Rest\RequestParam(name="pdm_date_fin_prevu", nullable=true)
     *
     * @Rest\RequestParam(name="rf_date_signature_autorisation_engagement", nullable=true)
     * @Rest\RequestParam(name="rf_autorisation_engagement", nullable=true)
     * @Rest\RequestParam(name="rf_credit_payement_annee_en_cours", nullable=true)
     * @Rest\RequestParam(name="rf_montant_depenses_decaisees_mandate", nullable=true)
     * @Rest\RequestParam(name="rf_montant_depenses_decaisees_liquide", nullable=true)
     * @Rest\RequestParam(name="rf_exercice_budgetaire", nullable=true)
     * @Rest\RequestParam(name="situation_projet", nullable=false)
     * @Rest\RequestParam(name="avancement", nullable=true)
     * @Rest\RequestParam(name="observation", nullable=true)
     * @Rest\RequestParam(name="photos", nullable=true)
     * @Rest\RequestParam(name="document", nullable=true)
     *
     * @return JsonResponse
     */
    public function editProject(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = new JsonResponse();
        $retour = $porojetService->saveProject($paramFetcher, $this->getUser(), true);
        $response->setData($retour);
        return $response;
    }

    /**
     * Liste les types de zone geo
     * @Rest\Get("/api/listTypeZone", name ="api_list_type_zone")
     * @return JsonResponse
     */
    public function getTypeZone(ProjetService $porojetService)
    {
        $response = $porojetService->listTypeZone();
        return $response;
    }

    /**
     * Liste les types de projet
     * @Rest\Get("/api/listTypeProjet", name ="api_list_type_projet")
     * @return JsonResponse
     */
    public function getTypeProjet(ProjetService $porojetService)
    {
        $response = $porojetService->listTypeProjet();
        return $response;
    }

    /**
     * Liste des zones géo
     * @Rest\Post("/api/listZoneByType", name ="api_list_zone")
     * @Rest\RequestParam(name="type_zone", nullable=false)
     * @return JsonResponse
     */
    public function getZone(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->listZone($paramFetcher);
        return $response;
    }

    /**
     * Liste des zones géo
     * @Rest\Post("/api/listZoneHierarchique", name ="api__listzone_hierarchique")
     * @Rest\RequestParam(name="type_zone", nullable=false)
     * @return JsonResponse
     */
    public function getZoneHierarchique(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->listZone($paramFetcher);
        return $response;
    }

    /**
     * Liste des categories
     * @Rest\Get("/api/listCategorie", name ="api_list_categorie")
     * @return JsonResponse
     */
    public function getCategorie(ProjetService $porojetService)
    {
        $response = $porojetService->listCategorie(false);
        return $response;
    }

    /**
     * Liste des engagements
     * @Rest\Get("/api/listEngagement", name ="api_list_engagement")
     * @return JsonResponse
     */
    public function getEngagement(ProjetService $porojetService)
    {
        $response = $porojetService->listEngagement(false);
        return $response;
    }

    /**
     * Liste type d'administration
     * @Rest\Get("/api/listTypeAdministration", name ="api_list_type_administration")
     * @return JsonResponse
     */
    public function getTypeAdministration(ProjetService $porojetService)
    {
        $response = $porojetService->listTypeAdministration();
        return $response;
    }


    /**
     * Liste des types de document
     * @Rest\Get("/api/listTypeDocument", name ="api_list_type_document")
     * @return JsonResponse
     */
    public function getDocType(ProjetService $porojetService)
    {
        $response = $porojetService->listDocType();
        return $response;
    }

    /**
     * Liste des services
     * @Rest\Get("/api/listSecteur", name ="api_list_secteur")
     * @return JsonResponse
     */
    public function getSecteur(ProjetService $porojetService)
    {
        $response = $porojetService->listSecteur(false);
        return $response;
    }

    /**
     * Liste des services
     * @Rest\Get("/api/listPriorite", name ="api_list_priorite")
     * @return JsonResponse
     */
    public function getPriorite(ProjetService $porojetService)
    {
        $response = $porojetService->listPrioriteProjet(false);
        return $response;
    }

    /**
     * Liste des statut projet
     * @Rest\Get("/api/listStatutProjet", name ="api_list_statut_projet")
     * @return JsonResponse
     */
    public function getStatutProjet(ProjetService $porojetService)
    {
        $response = $porojetService->listStatutProjet();
        return $response;
    }

    /**
     * Liste des situations projets
     * @Rest\Get("/api/listSituationProjet", name ="api_list_situation_projet")
     * @return JsonResponse
     */
    public function getSituationProjet(ProjetService $porojetService)
    {
        $response = $porojetService->listSituationProjet();
        return $response;
    }

    /**
     * Liste des elements d'une zone
     * @Rest\Post("/api/listZoneElementById", name ="api_get_zone_element_by_id")
     * @Rest\RequestParam(name="zone_id", nullable=false)
     * @Rest\RequestParam(name="type_id", nullable=true)
     * @return JsonResponse
     */
    public function getListZoneElementById(ParamFetcher $paramFetcher, ZoneService $zoneService)
    {
        $response = $zoneService->getListZoneElementById($paramFetcher);
        return $response;
    }


    /**
     * Liste les informations d'un zone de refernce
     * @Rest\Post("/api/getZoneById", name ="api_get_zone_by_id")
     * @Rest\RequestParam(name="zone_id", nullable=false)
     * @return JsonResponse
     */
    public function getZoneById(ParamFetcher $paramFetcher, ZoneService $zoneService)
    {
        $response = $zoneService->getZoneById($paramFetcher);
        return $response;
    }

    /**
     * upload les documents du projet
     * @Rest\Post("/api/importFile", name ="api_import_file")
     * @Rest\RequestParam(name="file", nullable=true)
     * @return JsonResponse
     */
    public function uploadFile(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->uploadMultypleFile($paramFetcher);
        return $response;
    }


    /**
     * @Rest\Post("/api/importPhotos")
     * @Rest\RequestParam(name="name", nullable=false)
     * @Rest\RequestParam(name="value", nullable=false)
     * @param ParamFetcher $paramFetcher
     * @throws \Exception
     */
    public function importPhotos(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->uploadPhotos($paramFetcher);
        return $response;
    }

    /**
     * Liste la liste des projets parents
     * @Rest\Get("/api/listProjetParent", name ="api_list_projet_parent")
     * @return JsonResponse
     */
    public function getListProjetParent(ProjetService $porojetService)
    {
        $response = $porojetService->getListProjetParent($this->getUser());
        return $response;
    }


    /**
     * Liste la liste des projets
     * @Rest\Post("/api/listProjet", name ="api_get_list_projet")
     * @Rest\RequestParam(name="nom", nullable=true)
     * @Rest\RequestParam(name="localite_emplacement", nullable=true)
     * @Rest\RequestParam(name="type_zone", nullable=true)
     * @Rest\RequestParam(name="statut", nullable=true)
     * @Rest\RequestParam(name="en_retard", nullable=true)
     * @Rest\RequestParam(name="projet_inaugurable", nullable=true)
     * @Rest\RequestParam(name="pdm_date_fin_prevu_debut", nullable=true)
     * @Rest\RequestParam(name="pdm_date_fin_prevu_fin", nullable=true)
     * @Rest\RequestParam(name="type_administration", nullable=true)
     * @Rest\RequestParam(name="administration", nullable=true)
     * @Rest\RequestParam(name="engagement", nullable=true)
     * @Rest\RequestParam(name="secteur", nullable=true)
     * @Rest\RequestParam(name="conv_cl", nullable=true)
     * @Rest\RequestParam(name="all", nullable=false)
     * @Rest\RequestParam(name="page", nullable=false)
     * @Rest\RequestParam(name="geo_ref", nullable=false)
     * @Rest\RequestParam(name="itemsPerPage", nullable=false)
     * @return JsonResponse
     */
    public function getListProjet(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->getListProjet($paramFetcher, $this->getUser(),false);
        return $response;
    }

    /**
     * Liste la liste des projets parents by user
     * @Rest\Post("/api/listProjetParentByUser", name ="api_get_list_projet_parent_by_user")
     * @Rest\RequestParam(name="nom", nullable=true)
     * @Rest\RequestParam(name="localite_emplacement", nullable=true)
     * @Rest\RequestParam(name="type_zone", nullable=true)
     * @Rest\RequestParam(name="statut", nullable=true)
     * @Rest\RequestParam(name="en_retard", nullable=true)
     * @Rest\RequestParam(name="projet_inaugurable", nullable=true)
     * @Rest\RequestParam(name="pdm_date_fin_prevu_debut", nullable=true)
     * @Rest\RequestParam(name="pdm_date_fin_prevu_fin", nullable=true)
     * @Rest\RequestParam(name="type_administration", nullable=true)
     * @Rest\RequestParam(name="administration", nullable=true)
     * @Rest\RequestParam(name="engagement", nullable=true)
     * @Rest\RequestParam(name="secteur", nullable=true)
     * @Rest\RequestParam(name="conv_cl", nullable=true)
     * @Rest\RequestParam(name="all", nullable=false)
     * @Rest\RequestParam(name="page", nullable=false)
     * @Rest\RequestParam(name="geo_ref", nullable=false)
     * @Rest\RequestParam(name="itemsPerPage", nullable=false)
     * @Rest\RequestParam(name="projet_parent", nullable=false)
     * @return JsonResponse
     */
    public function getListProjetParentByUser(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->getListProjet($paramFetcher, $this->getUser(),true);
        return $response;
    }

    /**
     * Get info projet
     * @Rest\Post("/api/getProjetById", name ="api_get_projet_by_id")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @return JsonResponse
     */
    public function getProjetById(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->getProjetById($paramFetcher, $this->getUser());
        return $response;
    }

    /**
     * Get min info projet
     * @Rest\Post("/api/getProjetByIdMinInfo", name ="api_get_projet_by_id_min_info")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @return JsonResponse
     */
    public function getProjetByIdMinInfo(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->getProjetByIdMinInfo($paramFetcher, $this->getUser());
        return $response;
    }


    /**
     * download les documents du projet
     * @Rest\Get("/api/downloadFile/{fileId}", name ="api_download_file")
     * @return JsonResponse
     */
    public function downloadFile($fileId, ProjetService $porojetService)
    {
        $response = $porojetService->downloadFile($fileId);
        return $response;
    }

    /**
     * retourne les documents de profil d'un projet
     * @Rest\Post("/api/getFileByIdProjet", name ="api_get_photos_by_id_projet")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @Rest\RequestParam(name="doc", nullable=false)
     * @return JsonResponse
     */
    public function getFileByIdProjet(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->getFileByIdProjet($paramFetcher);
        return $response;
    }

    /**
     * Liste titulaire du marcher
     * @Rest\Get("/api/listTitulaireMarcher", name ="api_list_titulaire_marcher")
     * @return JsonResponse
     */
    public function getTitulaireMarcher(ProjetService $porojetService)
    {
        $response = $porojetService->listTitulaireMarcher();
        return $response;
    }

    /**
     * Liste la liste des historiques projet
     * @Rest\Post("/api/listProjetHistory", name ="api_list_projet_history")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @return JsonResponse
     */
    public function getListProjetHistory(ParamFetcher $paramFetcher, CommunService $communService)
    {
        $response = $communService->getHistoryProject($paramFetcher);
        return $response;
    }

    /**
     * Get observation projet
     * @Rest\Post("/api/getObservationByProjetId", name ="api_get_observation_projet_by_id")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @return JsonResponse
     */
    public function getObservationByProjetId(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->listObsByProjetId($paramFetcher);
        return $response;
    }

    /**
     * Liste des etapes de validation
     * @Rest\Get("/api/getValidationEtape", name ="api_get_validation_etape")
     * @return JsonResponse
     */
    public function getValidationEtape(FluxService $fluxService)
    {
        $response = $fluxService->getValidationEtape();
        return $response;
    }

    /**
     * Poursuivre l'etape de validation d'un projet
     * @Rest\Post("/api/validationProjet", name ="api_validation_projet")
     * @Rest\RequestParam(name="profil_id", nullable=false)
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @Rest\RequestParam(name="institution_collecte", nullable=false)
     * @Rest\RequestParam(name="profil_valid", nullable=false)
     * @return JsonResponse
     */
    public function validerProjet(ParamFetcher $paramFetcher, FluxService $fluxService)
    {
        $response = $fluxService->saveAffectationProjet($paramFetcher, $this->getUser());
        return $response;
    }

    /**
     * liste des projets par pfofil
     * @Rest\Post("/api/listProjetByProfil", name ="api_list_projet_by_profil")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @return JsonResponse
     */
    public function listProjetByProfil(ParamFetcher $paramFetcher, FluxService $fluxService)
    {
        $response = $fluxService->saveAffectationProjet($paramFetcher, $this->getUser());
        return $response;
    }

    /**
     * liste des projets par utilisateur
     * @Rest\Get("/api/listProjetByUser", name ="api_list_projet_by_user")
     * @return JsonResponse
     */
    public function listProjetUser(FluxService $fluxService)
    {
        $response = $fluxService->getProjetByProfilUser($this->getUser());
        return $response;
    }

    /**
     * Sauvegarder categorie
     * @Rest\Post("/api/saveCategorie", name ="api_save_categorie")
     * @Rest\RequestParam(name="categorie", nullable=false)
     * @return JsonResponse
     */
    public function saveCategorie(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->saveCategorie($paramFetcher);
        return $response;
    }

    /**
     * Sauvegarder secteur
     * @Rest\Post("/api/saveSecteur", name ="api_save_secteur")
     * @Rest\RequestParam(name="secteur", nullable=false)
     * @return JsonResponse
     */
    public function saveSecteur(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->saveSecteur($paramFetcher);
        return $response;
    }

    /**
     * Sauvegarder engagement
     * @Rest\Post("/api/saveEngagement", name ="api_save_engagement")
     * @Rest\RequestParam(name="engagement", nullable=false)
     * @return JsonResponse
     */
    public function saveEngagement(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->saveEngagement($paramFetcher);
        return $response;
    }

    /**
     * Sauvegarder priorite
     * @Rest\Post("/api/savePriorite", name ="api_save_priorite")
     * @Rest\RequestParam(name="priorite", nullable=false)
     * @return JsonResponse
     */
    public function savePriorite(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->savePriorite($paramFetcher);
        return $response;
    }

    /**
     * Liste profil par droit
     * @Rest\Post("/api/getProfilByIdDroit", name ="api_get_profil_by_id_droit")
     * @Rest\RequestParam(name="droit_id", nullable=false)
     * @return JsonResponse
     */
    public function getProfilByIdDroit(ParamFetcher $paramFetcher, FluxService $fluxService)
    {
        $response = $fluxService->getProfilByIdDroit($paramFetcher, $this->getUser());
        return $response;
    }

    /**
     * Liste la liste des projets dashboard
     * @Rest\Get("/api/listProjetDashboard", name ="api_get_list_projet_dashboard")
     * @return JsonResponse
     */
    public function getListProjetDashboard(ProjetService $porojetService)
    {
        $response = $porojetService->getListProjetDashboard($this->getUser());
        return $response;
    }

    /**
     * Dashboard kpi
     * @Rest\Post("/api/getKpiOrMinInfoProjet", name ="api_get_kpi_or_min_info_projet")
     * @Rest\RequestParam(name="total", nullable=false)
     * @Rest\RequestParam(name="restant", nullable=false)
     * @Rest\RequestParam(name="encours", nullable=false)
     * @Rest\RequestParam(name="retard", nullable=false)
     * @Rest\RequestParam(name="realise", nullable=false)
     * @Rest\RequestParam(name="surcout", nullable=false)
     * @Rest\RequestParam(name="montant", nullable=false)
     * @Rest\RequestParam(name="service", nullable=false)
     * @Rest\RequestParam(name="kpiExist", nullable=false)
     * @Rest\RequestParam(name="nomProjet", nullable=true)
     * @Rest\RequestParam(name="typeAdmin", nullable=true)
     * @Rest\RequestParam(name="administration", nullable=true)
     * @Rest\RequestParam(name="statut", nullable=true)
     * @Rest\RequestParam(name="dateFinPrevdebut", nullable=true)
     * @Rest\RequestParam(name="dateFinPrevfin", nullable=true)
     * @Rest\RequestParam(name="secteur", nullable=true)
     * @Rest\RequestParam(name="engagement", nullable=true)
     * @Rest\RequestParam(name="region", nullable=true)
     * @Rest\RequestParam(name="enRetard", nullable=true)
     * @Rest\RequestParam(name="inaugurable", nullable=true)
     * @Rest\RequestParam(name="montantEngage", nullable=true)
     * @Rest\RequestParam(name="montantDecaisse", nullable=true)
     * @return JsonResponse
     */
    public function getKpiOrMinInfoProjet(ParamFetcher $paramFetcher, KpiProjetService $kpiService)
    {
        $response = $kpiService->getKpiOrMinInfoProjet($paramFetcher, $this->getUser());
        return $response;
    }

    /**
     * Liste la liste des sous projet
     * @Rest\Post("/api/listSousProjet", name ="api_list_sous_projet")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @Rest\RequestParam(name="geo_ref", nullable=false)
     * @return JsonResponse
     */
    public function getListSousProjet(ParamFetcher $paramFetcher, ProjetService $porojetService)
    {
        $response = $porojetService->getListSousProjet($paramFetcher,$this->getUser());
        return $response;
    }

    /**
     * Get montant by projet id
     * @Rest\Post("/api/getMontantByProjetId", name ="api_get_montant_by_projet_id")
     * @Rest\RequestParam(name="projet_id", nullable=false)
     * @return JsonResponse
     */
    public function getMontantByProjetId(ParamFetcher $paramFetcher, CommunService $commun)
    {
        $response = $commun->getMontantByProjetId($paramFetcher);
        return $response;
    }
}