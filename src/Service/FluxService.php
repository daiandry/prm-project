<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 13/08/2020
 * Time: 15:06
 */

namespace App\Service;


use App\Entity\PrmAdministration;
use App\Entity\PrmAffectationProjet;
use App\Entity\PrmCategorieProjet;
use App\Entity\PrmDroit;
use App\Entity\PrmEngagement;
use App\Entity\PrmEtapeAffectation;
use App\Entity\PrmPrioriteProjet;
use App\Entity\PrmProfil;
use App\Entity\PrmProjet;
use App\Entity\PrmSecteur;
use App\Entity\PrmSituationProjet;
use App\Entity\PrmStatutProjet;
use App\Entity\PrmTypeProjet;
use App\Entity\PrmTypeZone;
use App\Entity\PrmZoneGeo;
use App\Entity\User;
use App\Utils\ConstantSrv;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class FluxService
{
    protected $container;
    protected $entityManager;
    protected $trans;
    protected $security;
    protected $projet;
    protected $templating;
    protected $mailer;

    /**
     * SearchService constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, TranslatorInterface $translator, Security $security, ProjetService $projet, Mailer $mailer)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->trans = $translator;
        $this->security = $security;
        $this->projet = $projet;
        $this->templating = $container->get('twig');
        $this->mailer = $mailer;
    }

    /**
     * @return JsonResponse
     */
    public function getValidationEtape()
    {
        $response = new JsonResponse();
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => []);
        $response->setData($data);
        return $response;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param User $user
     * @return JsonResponse
     */
    public function saveAffectationProjet(ParamFetcher $paramFetcher, User $user)
    {
        try {
            $response = new JsonResponse();
            $firstValidation = false;
            $aIdProfil = $paramFetcher->get('profil_id');
            $idProjet = $paramFetcher->get('projet_id');
            $institutionCollecte = $paramFetcher->get('institution_collecte');
            $profil_valid = $paramFetcher->get('profil_valid');
            $repProjet = $this->entityManager->getRepository(PrmProjet::class);
            $repProfil = $this->entityManager->getRepository(PrmProfil::class);
            $repStatut = $this->entityManager->getRepository(PrmStatutProjet::class);
            $oProjet = $repProjet->findOneBy(array('id' => $idProjet));
            if ($oProjet->getStatut()->getId() == ConstantSrv::STATUT_PROJET_TERMINE) {
                $data = array('code' => ConstantSrv::CODE_BAD_REQUEST, 'Message' => $this->trans->trans('project_achieve'));
            } else {
                //-- check existance affctation et incremente or initialize where not exist
                $valide = 0;
                $repAffect = $this->entityManager->getRepository(PrmAffectationProjet::class);
                $niveauMax = $repAffect->findLastAffectationByProjet($oProjet->getId());
                $niveauMax = (isset($niveauMax[1])) ? $niveauMax[1] : null;
                $oStatutProjet = $this->projet->manageStatutProjet($oProjet->getSituationActuelleMarche()->getId());
                if ($niveauMax != null) {
                    $valide = 1;
                    $profilValide = $this->projet->getLastNiveauAndvalidate($niveauMax, $user);
                    $niveau = $niveauMax + 1;
                    $repProjet->updateStatutProjet($idProjet, $oStatutProjet->getId());
                    $oProjet->setValide($valide);
                    $this->entityManager->persist($oProjet);
                    $this->entityManager->flush();
                } else {
                    $oStatutValid = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_ENCOURS_VALIDATION));
                    $repProjet->updateStatutProjet($idProjet, $oStatutValid->getId());
                    $niveau = 1;
                    $profilValide = true;
                    if ($profil_valid == true) {
                        $valide = 1;
                        $oAffectation = new PrmAffectationProjet();
                        $oAffectation->setUser($user);
                        $oAffectation->setProjet($oProjet);
                        $oAffectation->setProfil($user->getProfil());
                        $oAffectation->setDateAffectation($this->projet->dateNow());
                        $oAffectation->setDateValidation($this->projet->dateNow());
                        $oAffectation->setValide($valide);
                        $oAffectation->setNiveau($niveau);
                        $oAffectation->setAdministration($user->getAdministration());
                        $oAffectation->setRegion($user->getRegion());
                        $oAffectation->setProfilValide($valide);
                        $oAffectation->setIsInstitutionCollect($institutionCollecte);
                        $this->entityManager->persist($oAffectation);
                        $oProjet->setValide($valide);
                        $this->entityManager->persist($oProjet);
                        $this->entityManager->flush();
                        $repProjet->updateStatutProjet($idProjet, $oStatutProjet->getId());
                        $niveau = 2;
                        $valide = 0;
                        $firstValidation = true;
                    }
                }
                if ($profilValide == true) {
                    foreach ($aIdProfil as $idProfil) {
                        $oProfil = $repProfil->findOneBy(array('id' => $idProfil));
                        if (($oProjet instanceof PrmProjet) && ($oProfil instanceof PrmProfil)) {

                            foreach ($oProfil->getUsers() as $oUserProjet) {
                                $oAffectation = new PrmAffectationProjet();
                                if ($institutionCollecte) {
                                    if ($oUserProjet->getAdministration() == $user->getAdministration()) {
                                        $oAffectation->setUser($oUserProjet);
                                    } else {
                                        $oAffectation->setUser($user);
                                    }
                                } else {
                                    $oAffectation->setUser($oUserProjet);
                                }
                                $oAffectation->setProjet($oProjet);
                                $oAffectation->setProfil($institutionCollecte?$oProfil:$oUserProjet->getProfil());
                                $oAffectation->setDateAffectation($this->projet->dateNow());
                                $oAffectation->setDateValidation(null);
                                $oAffectation->setValide($valide);
                                $oAffectation->setNiveau($niveau);
                                $oAffectation->setIsInstitutionCollect($institutionCollecte);
                                $oAffectation->setAdministration($user->getAdministration());
                                $oAffectation->setRegion($user->getRegion());
                                $this->entityManager->persist($oAffectation);
                                $this->entityManager->flush();
                                $envoi = true;
                            }
                        } else {
                            $envoi = false;
                            break;
                        }
                    }
                    $this->entityManager->flush();
                    //-- send mail --
                    if ($envoi == true) {
                        $this->sendMailValidate($user, $oProjet, $aIdProfil, $institutionCollecte, $niveau, $profil_valid, $firstValidation);
                        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'));
                    } else {
                        $data = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('mail_not_send'));
                    }
                    $oProjet->setValide(true);
                    $this->entityManager->persist($oProjet);
                    $this->entityManager->flush();
                } else {
                    $data = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('profil_not_found'));
                }
            }
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * @param User $user
     * @param $oProject
     * @param $aIdProfil
     * @param $institutionCollecte
     * @param $niveau
     * @param bool $profil_valid
     * @param bool $firstValidation
     */
    public function sendMailValidate(User $user, $oProject, $aIdProfil, $institutionCollecte, $niveau, $profil_valid = true, $firstValidation = false)
    {
        try {
            if ($oProject instanceof PrmProjet) {
                $result = [];
                $emailInitiator = [];
                $repAffect = $this->entityManager->getRepository(PrmAffectationProjet::class);
                if (($niveau == 2) && ($firstValidation == true)) {
                    array_push($result, $user->getProfil()->getLibelle());
                    $allAffect = $repAffect->getListValidateurProjet($oProject->getId());
                } else if ($niveau == 1) {
                    $allAffect = $repAffect->getListValidateurProjetInProfil($oProject->getId(), $aIdProfil);
                } else {
                    $allAffect = $repAffect->getListValidateurProjet($oProject->getId());
                }
                if (!empty($allAffect)) {
                    foreach ($allAffect as $oAffect) {
                        if ($oAffect instanceof PrmAffectationProjet) {
                            $entityName = $oAffect->getProfil()->getCode();
                            array_push($result, $entityName);
                            ($niveau == 1) ? array_push($emailInitiator, $oAffect->getUser()->getEmail()) : null;
                        }
                    }
                    $texteValider = 'a été validé par :';
                    $texteAffecter = 'a été affecté par :';
                    $nomProjet = $oProject->getNom();
                    $body = $this->templating->render('projet/projet_valide_send_mail.html.twig', [
                        'nom' => $nomProjet,
                        'list_validateur' => $result,
                        'texte' => ($profil_valid == false) ? $texteAffecter : $texteValider
                    ]);
                    $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
                    $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;
                    $mailCollab = ($institutionCollecte == true) ? $this->projet->getMailParcourProjet($oProject, $administration, $region, $institutionCollecte, $user, $aIdProfil) : $this->getUserMailByProfil($aIdProfil, $administration, $region);
                    $mailCollab = array_merge($mailCollab, array_unique($emailInitiator));
                    if (!empty($mailCollab)) {
                        $this->mailer->sendMailWithoutParm($mailCollab, $subject = "Projet Validé", $body);
                    }
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $aIdProfil
     * @return array
     */
    public function getUserMailByProfil($aIdProfil, $administration, $region)
    {
        $result = [];
        $rep = $this->entityManager->getRepository(User::class);
        $aMailUser = $rep->getUserMailByProfil($aIdProfil);
        if (!empty($aMailUser)) {
            foreach ($aMailUser as $aMail) {
                array_push($result, $aMail['email']);
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @param bool $zoneExist
     * @return array
     */
    public function sendZone($data = null, $zoneExist = false)
    {
        if ($zoneExist) {
            $results = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $data);
        } else {
            $results = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('zone_not_found'));
        }
        return $results;
    }

    /**
     * @param User $user
     */
    public function getProjetByProfilUser(User $user)
    {
        $data = [];
        $repProjet = $this->entityManager->getRepository(PrmProjet::class);
        $repUser = $this->entityManager->getRepository(User::class);
        $oUser = $repUser->find($user);
        if ($oUser->getProfil() instanceof PrmProfil) {
            $allProjet = $repProjet->getProjetByProfil();
        }
        if (!empty($allProjet)) {

        }
        dump($oUser->getProfil());
        die;
    }

    /**
     * @param ParamFetcher $paramFetcher
     */
    public function getProfilByIdDroit(ParamFetcher $paramFetcher, User $user)
    {
        try {
            $response = new JsonResponse();
            $result = [];
            $aIdDroit = $paramFetcher->get('droit_id');
            if (is_array($aIdDroit) && !empty($aIdDroit)) {
                $rep = $this->entityManager->getRepository(PrmDroit::class);
                foreach ($aIdDroit as $idDroit) {
                    $oDroit = $rep->findOneBy(array('id' => $idDroit));
                    if ($oDroit instanceof PrmDroit) {
                        $aProfil = $rep->getProfilByDroit($idDroit, $user->getId());
                        foreach ($aProfil as $profil) {
                            array_push($result, $profil);
                        }
                    }
                }
            }
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
     