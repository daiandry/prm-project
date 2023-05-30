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
use App\Entity\PrmDocType;
use App\Entity\PrmDocuments;
use App\Entity\PrmEngagement;
use App\Entity\PrmObservationProjet;
use App\Entity\PrmPhotos;
use App\Entity\PrmPrioriteProjet;
use App\Entity\PrmProfil;
use App\Entity\PrmProjet;
use App\Entity\PrmSecteur;
use App\Entity\PrmSituationProjet;
use App\Entity\PrmStatutProjet;
use App\Entity\PrmTitulaireMarcher;
use App\Entity\PrmTypeAdmin;
use App\Entity\PrmTypeProjet;
use App\Entity\PrmTypeZone;
use App\Entity\PrmZoneGeo;
use App\Entity\User;
use App\Event\ProjetEvent;
use App\Utils\ConstantSrv;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProjetService
{
    protected $container;
    protected $entityManager;
    protected $trans;
    protected $security;
    protected $serviceFileUpload;
    protected $mailer;
    protected $zone;
    protected $templating;
    protected $commun;

    /**
     * SearchService constructor.
     * @param ContainerInterface $container
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, TranslatorInterface $translator, Security $security, FileUpload $serviceFileUpload, Mailer $mailer, ZoneService $zone, CommunService $commun)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
        $this->trans = $translator;
        $this->security = $security;
        $this->serviceFileUpload = $serviceFileUpload;
        $this->mailer = $mailer;
        $this->zone = $zone;
        $this->templating = $container->get('twig');
        $this->commun = $commun;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param User $user
     * @param bool $edit
     * @return array
     */
    public function saveProject(ParamFetcher $paramFetcher, User $user, $edit = false)
    {
        try {
            $results = [];
            $rep = $this->entityManager->getRepository(PrmProjet::class);
            $repZone = $this->entityManager->getRepository(PrmZoneGeo::class);
            $idZone = $paramFetcher->get('localite_emplacement');
            $oZone = $repZone->findOneBy(array('id' => $idZone));

            $idProjet = ($edit == true) ? $paramFetcher->get('id') : null;
            $montantEngage = $paramFetcher->get('rf_autorisation_engagement');
            $creditDepenseAnneeEncours = $paramFetcher->get('rf_credit_payement_annee_en_cours');
            $depenseDecaisseMandate = $paramFetcher->get('rf_montant_depenses_decaisees_mandate');
            $depenseDecaisseLiquide = $paramFetcher->get('rf_montant_depenses_decaisees_liquide');
            if (($edit == true) && ($idProjet != null)) {
                $oProject = $rep->findOneBy(array('id' => $idProjet));
            } else {
                $oProject = $rep->findOneBy(array('soa_code' => $paramFetcher->get('soa_code')));
            }
            if (($edit == false) && ($oProject instanceof PrmProjet)) {
                $results = array('code' => ConstantSrv::CODE_DUPLICATE_RESSOURCE, 'Message' => $this->trans->trans('duplicate_project'));
            } else if (($edit == true) && (!$oProject instanceof PrmProjet)) {
                $results = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('projet_not_found'));
            } elseif (($paramFetcher->get('avancement') > 100) || ($paramFetcher->get('avancement') < 0)) {
                $results = array('code' => ConstantSrv::CODE_BAD_REQUEST, 'Message' => $this->trans->trans('invalide_pourcentage'));
            } elseif (!$oZone instanceof PrmZoneGeo) {
                $results = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('zone_not_found'));
            } elseif (($creditDepenseAnneeEncours > $montantEngage) || ($depenseDecaisseMandate > $montantEngage) || ($depenseDecaisseLiquide > $montantEngage)) {
                $results = array('code' => ConstantSrv::CODE_BAD_REQUEST, 'Message' => $this->trans->trans('invalide_montant'));
            } elseif (($edit == true) && ($oProject instanceof PrmProjet) && ($oProject->getStatut()->getId() == ConstantSrv::STATUT_PROJET_TERMINE)) {
                $results = array('code' => ConstantSrv::CODE_BAD_REQUEST, 'Message' => $this->trans->trans('project_achieve'));
            } else {
                try {
                    if ($edit == false) {
                        $oProject = new PrmProjet();
                        $oProject->setDateCreate($this->dateNow());
                        $oProject->setDateModify($this->dateNow());
                        $oProject->setEnRetard(false);
                        $oProject = $this->setProject($paramFetcher, $oProject, $user, false);
                        $oProject = $this->setProjectEntityProper($paramFetcher, $oProject);
                        // set first validation
                        $valide = 1;
                        $oAffectation = new PrmAffectationProjet();
                        $oAffectation->setUser($user);
                        $oAffectation->setProjet($oProject);
                        $oAffectation->setProfil($user->getProfil());
                        $oAffectation->setDateAffectation(new \DateTime());
                        $oAffectation->setDateValidation(new \DateTime());
                        $oAffectation->setValide($valide);
                        $oAffectation->setNiveau(0);
                        $oAffectation->setAdministration($user->getAdministration());
                        $oAffectation->setRegion($user->getRegion());
                        $oAffectation->setProfilValide($valide);
                        $oAffectation->setIsInstitutionCollect(true);
                        $this->entityManager->persist($oAffectation);
                        $this->entityManager->flush();

                        $this->entityManager->persist($oProject);
                    } else {
                        $iAvancement = $oProject->getAvancement();
                        $sObsOld = $oProject->getObservation();
                        $oProject->setDateModify($this->dateNow());
                        $oProject = $this->setProject($paramFetcher, $oProject, $user, true);
                        $val = " " . $oProject->getCoordonneGPS();
                        $oProject->setCoordonneGPS($val);
                        $oProject = $this->setProjectEntityProper($paramFetcher, $oProject);
                    }
                    $aPhotos = $paramFetcher->get('photos');
                    $aDocs = $paramFetcher->get('document');
                    $dateNow = $this->dateNow();
                    $pathPhotos = $this->container->getParameter('import_path_photos');
                    $pathDoc = $this->container->getParameter('import_path');
                    if (isset($aPhotos) && !empty($aPhotos) && is_array($aPhotos)) {
                        $oPhotoUpdate = (($edit == true) && isset($aPhotos[0]['id'])) ? $this->photosManage($aPhotos[0]['id']) : null;
                        if ($oPhotoUpdate instanceof PrmPhotos) {
                            $oPhotos = $oPhotoUpdate;
                            $this->unlinkFile($pathPhotos . $oPhotos->getNom());
                        } else {
                            $oPhotos = new PrmPhotos();
                        }
                        foreach ($aPhotos as $aPhoto) {
                            $aPhoto['nom'] = $this->nameUpload($aPhoto['nom']);
                            $oPhotos = $this->addPhotos($aPhoto, $dateNow, $oPhotos, $pathPhotos);
                            break;
                        }
                        $oPhotos->setProjet($oProject);
                        $this->uploadFile($aPhoto['nom'], $aPhoto['value'], $pathPhotos);
                        $this->entityManager->persist($oPhotos);
                    } else {
                        $this->deletePhotos($oProject, $pathPhotos);
                    }
                    //doc manage
                    if (isset($aDocs) && !empty($aDocs) && is_array($aDocs)) {
                        ($edit == true) ? $this->flagDocument($oProject) : null;
                        foreach ($aDocs as $aDoc) {
                            $oDocUpdate = (($edit == true) && isset($aDoc['id'])) ? $this->docManage($aDoc['id']) : null;
                            if ($oDocUpdate instanceof PrmDocuments) {
                                $oDoc = $oDocUpdate;
                                $this->unlinkFile($pathDoc . $oDoc->getNom());
                            } else {
                                $oDoc = new PrmDocuments();
                            }
                            $aDoc['nom'] = $this->nameUpload($aDoc['nom']);
                            $oDoc = $this->addDocuments($aDoc, $dateNow, $oDoc, $pathDoc);
                            $oDoc->setProjet($oProject);
                            $this->uploadFile($aDoc['nom'], $aDoc['value'], $pathDoc);
                            $this->entityManager->persist($oDoc);
                        }
                    };
                    $this->entityManager->flush();
                    if ($edit == true) {
                        $this->deleteDocument($oProject, $pathDoc);
                        $repStatut = $this->entityManager->getRepository(PrmStatutProjet::class);
                        $oStatut = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_TERMINE));
                        $termine = true;
                        $mailCollab = $this->getMailParcourProjet($oProject, null, null, false, null, [], $termine);

                        if ((
                                ($oProject->getStatut() == $oStatut) && (($oProject->isMailAchever() == false) || ($oProject->isMailAchever() == null))
                            )
                            || (($oProject->isInaugurable() == true) && (($oProject->isMailInaugurable() == false) || ($oProject->isMailInaugurable() == null)))
                        ) {
                        } else {
                            $dispatcher = new EventDispatcher();
                            $dispatcher->addListener(ProjetEvent::NAME, [$this->container->get('app.projet_listener'), 'onChangedSendMail']);
                            $dispatcher->dispatch(new ProjetEvent($oProject), ProjetEvent::NAME);
                        }

                        (($oProject->getStatut() == $oStatut) && (($oProject->isMailAchever() == false) || ($oProject->isMailAchever() == null))) ? $this->sendMailEdit($oProject, $mailCollab, ConstantSrv::STATUT_PROJET_TERMINE) : null;

                        (($oProject->isInaugurable() == true) && (($oProject->isMailInaugurable() == false) || ($oProject->isMailInaugurable() == null))) ? $this->sendMailEdit($oProject, $mailCollab, ConstantSrv::STATUT_PROJET_INAUGURABLE) : null;

                        $this->observationCheck($sObsOld, $oProject, $user);
                        $repAffect = $this->entityManager->getRepository(PrmAffectationProjet::class);
                        $niveauMax = $repAffect->findLastAffectationByProjet($oProject->getId());
                        $niveauMax = (isset($niveauMax[1])) ? $niveauMax[1] : null;
                        if (isset($niveauMax) && ($niveauMax != null) && ($oProject->getStatut() == $oStatut)) {
                            $profilValide = $this->getLastNiveauAndvalidate($niveauMax, $user);
                            if ($profilValide == false) {
                                $oStatut = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_EN_COURS));
                                $oProject->setStatut($oStatut);
                            }
                        }
                        //-- check avancement change --
                        if ($iAvancement != $oProject->getAvancement()) {
                            $this->commun->setHistoriqueAvancementProjet($oProject);
                        }
                    } else {
                        $this->observationCheck(null, $oProject, $user);
                    }
                    $results = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'));
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $results = array('error' => array('error_code' => ConstantSrv::CODE_UNAUTHORIZED, 'error_line' => $e->getLine(), 'message' => $e->getMessage()));
        }
        return $results;
    }

    /**
     * @param $niveauMax
     */
    public function getLastNiveauAndvalidate($niveauMax, $user)
    {
        $repAffect = $this->entityManager->getRepository(PrmAffectationProjet::class);
        $oldInstance = $repAffect->findBy(array('niveau' => $niveauMax));
        foreach ($oldInstance as $oInstance) {
            if ($user->getProfil() == $oInstance->getProfil()) {
                $profilExist = true;
                break;
            } else {
                $profilExist = false;
            }
        }
        if ($profilExist == true) {
            foreach ($oldInstance as $oInstance) {
                if ($oInstance instanceof PrmAffectationProjet) {
                    $oInstance = $this->setLastProfilValidate($oInstance, $user);
                    $this->entityManager->persist($oInstance);
                    $this->entityManager->flush();
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param PrmAffectationProjet $oInstance
     * @param $user
     * @return PrmAffectationProjet*
     */
    public function setLastProfilValidate(PrmAffectationProjet $oInstance, $user)
    {
        if ($user instanceof User) {
            if (($user->getProfil() instanceof PrmProfil) && ($user->getProfil() == $oInstance->getProfil())) {
                $oInstance->setDateValidation($this->dateNow());
                $oInstance->setValide(1);
                $oInstance->setProfilValide(1);
            }
        }
        return $oInstance;
    }

    /**
     * @param PrmProjet $oProject
     */
    public function flagDocument(PrmProjet $oProject)
    {
        $rep = $this->entityManager->getRepository(PrmDocuments::class);
        $allDoc = $rep->findBy(array('projet' => $oProject));
        if (!empty($allDoc)) {
            foreach ($allDoc as $oDoc) {
                $oDoc->setEnabled(false);
                $this->entityManager->persist($oDoc);
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param PrmProjet $oProject
     * @param $pathDoc
     */
    public function deleteDocument(PrmProjet $oProject, $pathDoc)
    {
        $rep = $this->entityManager->getRepository(PrmDocuments::class);
        $allDoc = $rep->findBy(array('projet' => $oProject, 'enabled' => false));
        if (!empty($allDoc)) {
            foreach ($allDoc as $oDoc) {
                if ($oDoc instanceof PrmDocuments) {
                    $this->unlinkFile($pathDoc . $oDoc->getNom());
                    $this->entityManager->remove($oDoc);
                    $this->entityManager->flush();
                }
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @param PrmProjet $oProject
     * @param $path
     */
    public function deletePhotos(PrmProjet $oProject, $path)
    {
        $rep = $this->entityManager->getRepository(PrmPhotos::class);
        $all = $rep->findBy(array('projet' => $oProject));
        if (!empty($all)) {
            foreach ($all as $oPht) {
                if ($oPht instanceof PrmPhotos) {
                    $this->unlinkFile($path . $oPht->getNom());
                    $this->entityManager->remove($oPht);
                    $this->entityManager->flush();
                }
            }
        }
    }

    /**
     * @param PrmProjet $oProject
     * @return array
     */
    public function getMailParcourProjet(PrmProjet $oProject, $administration = null, $region = null, $institutionCollecte = false, User $user = null, $aProfils = [], $termine = false)
    {
        $mailCollab = [];
        $repAffect = $this->entityManager->getRepository(PrmAffectationProjet::class);


        if (!$termine) {
            /**
             * get all user have equal (administration or region) and profil
             */
            if ($institutionCollecte && $user instanceof User) {
                $repUser = $this->entityManager->getRepository(User::class);
                if (count($aProfils) > 0) {
                    $toUsers = $repUser->findUserByAdminOrRegionAndProfil($user, $aProfils);
                    foreach ($toUsers as $oUser) {
                        array_push($mailCollab, $oUser['email']);
                    }
                }
            }
        }
        $allAffect = $repAffect->findBy(array('projet' => $oProject));

        if (!empty($allAffect)) {
            foreach ($allAffect as $oAffect) {
                if ($oAffect instanceof PrmAffectationProjet) {

                    if ($termine) {

                        foreach ($oAffect->getProfil()->getUsers() as $u) {

                            if (in_array('ROLE_KPIS_POUR_CSA/CP', $u->getRoles())) {
                                $mail = $u->getEmail();
                                array_push($mailCollab, $mail);
                            }
                        }

                    } else {

                        $mail = $oAffect->getUser()->getEmail();
                        array_push($mailCollab, $mail);
                    }
                }

            }
        }
        array_push($mailCollab, $oProject->getCreatedBy()->getEmail());
        return array_unique($mailCollab);
    }

    /**
     * @param PrmProjet $oProject
     * @return array
     */
    public function getMailParcourProjetValidation(PrmProjet $oProject, $administration, $region, $profilValide = false)
    {
        $mailCollab = [];
        $repAffect = $this->entityManager->getRepository(PrmAffectationProjet::class);
        $mailUserInProfil = $repAffect->getAllMailInProfilAffectation($oProject->getId(), $administration, $region, $profilValide);
        if (!empty($mailUserInProfil)) {
            foreach ($mailUserInProfil as $user) {
                array_push($mailCollab, $user['email']);
            }
        }
        array_push($mailCollab, $oProject->getCreatedBy()->getEmail());
        return $mailCollab;
    }

    /**
     * @param $oProject
     */
    public function sendMailEdit($oProject, $mailCollab, $statut)
    {
        try {
            if (($oProject instanceof PrmProjet) && !empty($statut)) {
                $context = SerializationContext::create()->setGroups('mail:inaugurable');
                $context->setSerializeNull(true);
                $serializer = SerializerBuilder::create()->build();
                $results = json_decode($serializer->serialize($oProject, 'json', $context));
                $results = $serializer->toArray($results);
                $data['nom'] = (isset($results['nom'])) ? $results['nom'] : "";
                $data['intitule_du_marcher'] = (isset($results['pdm_titulaire_du_marche']['nom'])) ? $results['pdm_titulaire_du_marche']['nom'] : "";
                $data['localisation'] = (isset($results['zone'][0]['libelle'])) ? $results['zone'][0]['libelle'] : "";
                $data['secteur'] = (isset($results['secteur']['libelle'])) ? $results['secteur']['libelle'] : "";
                $data['engagement'] = (isset($results['engagement']['libelle'])) ? $results['engagement']['libelle'] : "";
                $data['lien_projet'] = (isset($_ENV['HOST_FRONT_LIEN_PROJET'])) ? str_replace(':idProjet', $oProject->getId(), $_ENV['HOST_FRONT_LIEN_PROJET']) : "localhost";
                $data['id_projet'] = $oProject->getId();
                $data['id_user_create'] = (isset($results['created_by']['id'])) ? $results['created_by']['id'] : "";
                $data['mail_user_create'] = (isset($results['created_by']['email'])) ? $results['created_by']['email'] : null;
                if ($statut == ConstantSrv::STATUT_PROJET_TERMINE) {
                    $objet = "Projet achevé";
                    $statutProjet = "de statut Achevé.";
                } else {
                    $objet = "Projet inaugurable";
                    $statutProjet = "à Inaugurable.";
                }
                $body = $this->templating->render('projet/projet_send_mail_inaugurable.html.twig', [
                    'nom' => $data['nom'],
                    'intitule_du_marcher' => $data['intitule_du_marcher'],
                    'localisation' => $data['localisation'],
                    'secteur' => $data['secteur'],
                    'engagement' => $data['engagement'],
                    'lien_projet' => $data['lien_projet'],
                    'id_projet' => $data['id_projet'],
                    'statut_projet' => $statutProjet
                ]);
                if ($data['mail_user_create'] != null) {
                    $this->mailer->sendMailInaugurable($mailCollab, $objet, $body);
                    if ($statut == ConstantSrv::STATUT_PROJET_TERMINE) {
                        $oProject->setMailAchever(true);
                    } else {
                        $oProject->setMailInaugurable(true);
                    }
                    $this->entityManager->persist($oProject);
                    $this->entityManager->flush();
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $idPhotos
     * @return bool
     */
    public function photosManage($idPhotos)
    {
        $update = false;
        $file = $this->entityManager->getRepository(PrmPhotos::class)->find($idPhotos);
        if ($file instanceof PrmPhotos) {
            $update = $file;
        }
        return $update;
    }

    /**
     * @param $idDoc
     * @return bool
     */
    public function docManage($idDoc)
    {
        $update = false;
        $file = $this->entityManager->getRepository(PrmDocuments::class)->find($idDoc);
        if ($file instanceof PrmDocuments) {
            $update = $file;
        }
        return $update;
    }

    /**
     * @param $filePath
     */
    public function unlinkFile($filePath)
    {
        try {
            if (file_exists($filePath) && is_writable($filePath)) {
                unlink($filePath);
            }
        } catch (\Exception $e) {
            echo 'Directory error: ' . $e->getMessage();
        }
    }

    /**
     * @param $number
     * @return bool
     */
    public function numericCheck($number)
    {
        $val = (is_numeric($number)) ? true : false;
        return $val;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param PrmProjet $oProject
     * @return PrmProjet
     */
    public function setProject(ParamFetcher $paramFetcher, PrmProjet $oProject, User $user, $edit = false)
    {
        $aZone = [];
        $repTypeProjet = $this->entityManager->getRepository(PrmTypeProjet::class);
        $repSituation = $this->entityManager->getRepository(PrmSituationProjet::class);
        $repProjet = $this->entityManager->getRepository(PrmProjet::class);
        $repZone = $this->entityManager->getRepository(PrmZoneGeo::class);
        $repStatut = $this->entityManager->getRepository(PrmStatutProjet::class);
        $oStatutValid = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_ENCOURS_VALIDATION));
        //step 1
        $projet_parent_id = $paramFetcher->get('projet_parent_id');
        $zone = $paramFetcher->get('localite_emplacement');
        $engagement = $paramFetcher->get('engagement');
        $categorie = $paramFetcher->get('categorie');
        $priorite = $paramFetcher->get('priorite');
        $secteur = $paramFetcher->get('secteur');
        $type = $paramFetcher->get('type');
        $pdm_titulaire_du_marche = $paramFetcher->get('pdm_titulaire_du_marche');
        //step 3
        $situation_actuelle_marche = $paramFetcher->get('situation_projet');
        //object
        $oTypeProjet = $repTypeProjet->findOneBy(array('id' => $type));
        $oSituation = $repSituation->findOneBy(array('id' => $situation_actuelle_marche));
        $oProjetParent = $repProjet->findOneBy(array('id' => $projet_parent_id));
        foreach ($zone as $id) {
            $oZone = $repZone->findOneBy(array('id' => $id));
            if ($oZone instanceof PrmZoneGeo) {
                array_push($aZone, $oZone);
            }
        }
        //setter step 1
        $oProject->setProjet(($oProjetParent instanceof PrmProjet) ? $oProjetParent : null);
        $oProject->setZone($aZone);
        $engagement = $this->checkEngagementProjet($engagement);
        $oProject->setEngagement($engagement);
        $categorie = $this->checkCategorieProjet($categorie);
        $oProject->setCategorie($categorie);
        $priorite = $this->checkPrioriteProjet($priorite);
        $oProject->setPriorite($priorite);
        $secteur = $this->checkSecteurProjet($secteur);
        $oProject->setSecteur($secteur);
        $oProject->setType(($oTypeProjet instanceof PrmTypeProjet) ? $oTypeProjet : null);
        $oProject->setCreatedBy(($user instanceof User) ? $user : null);
        $oStatut = (($edit == true) && ($oProject->getStatut() == $oStatutValid)) ? $oStatutValid : $this->manageStatutProjet($situation_actuelle_marche);
        $oProject->setStatut($oStatut);
        //setter step 2
        $pdmTm = $this->checkTitulaireMarcherProjet($pdm_titulaire_du_marche);
        $oProject->setPdmTitulaireDuMarche($pdmTm);
        //setter step 3
        $oProject->setSituationActuelleMarche(($oSituation instanceof PrmSituationProjet) ? $oSituation : null);

        return $oProject;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param PrmProjet $oProject
     * @return PrmProjet
     */
    public function setProjectEntityProper(ParamFetcher $paramFetcher, PrmProjet $oProject)
    {
        //step 1
        $nom = $paramFetcher->get('nom');
        $conv_cl = $paramFetcher->get('conv_cl');
        $coordonnee_gps = $paramFetcher->get('coordonnee_gps');
        $soa_code = $paramFetcher->get('soa_code');
        $pcop_code = $paramFetcher->get('pcop_compte');
        $description = $paramFetcher->get('description');
        $prommesse_presidentielle = $paramFetcher->get('prommesse_presidentielle');
        $projet_inaugurable = $paramFetcher->get('projet_inaugurable');
        $date_inauguration = $paramFetcher->get('date_inauguration');
        $situation_actuelle_travaux = $paramFetcher->get('situation_actuelle_travaux');
        //step 2
        $pdm_date_debut_appel_offre = $paramFetcher->get('pdm_date_debut_appel_offre');
        $pdm_date_fin_offre = $paramFetcher->get('pdm_date_fin_offre');
        $pdm_date_signature_contrat = $paramFetcher->get('pdm_date_signature_contrat');
        $pdm_designation = $paramFetcher->get('pdm_designation');
        $pdm_tiers_nif = $paramFetcher->get('pdm_tiers_nif');
        $pdm_date_lancement_os = $paramFetcher->get('pdm_date_lancement_os');
        $pdm_date_lancement_travaux_prevu = $paramFetcher->get('pdm_date_lancement_travaux_prevu');
        $pdm_date_lancement_travaux_reel = $paramFetcher->get('pdm_date_lancement_travaux_reel');
        $pdm_delai_execution_prevu = $paramFetcher->get('pdm_delai_execution_prevu');
        $pdm_date_fin_prevu = $paramFetcher->get('pdm_date_fin_prevu');
        //step 3
        $rf_date_signature_autorisation_engagement = $paramFetcher->get('rf_date_signature_autorisation_engagement');
        $rf_autorisation_engagement = $paramFetcher->get('rf_autorisation_engagement');
        $rf_credit_payement_annee_en_cours = $paramFetcher->get('rf_credit_payement_annee_en_cours');
        $rf_montant_depenses_decaisees_mandate = $paramFetcher->get('rf_montant_depenses_decaisees_mandate');
        $rf_montant_depenses_decaisees_liquide = $paramFetcher->get('rf_montant_depenses_decaisees_liquide');
        $rf_exercice_budgetaire = $paramFetcher->get('rf_exercice_budgetaire');
        $situation_actuelle_marche = $paramFetcher->get('situation_projet');
        $avancement = $paramFetcher->get('avancement');
        $observation = $paramFetcher->get('observation');
        //setter step 1
        $oProject->setNom($nom);
        $oProject->setConvCl($conv_cl);
        $oProject->setCoordonneGPS(json_encode($coordonnee_gps));
        $oProject->setSoaCode($soa_code);
        $oProject->setPcopCompte($pcop_code);
        $oProject->setDescription($description);
        $oProject->setPromessePresidentielle($prommesse_presidentielle);
        $oProject->setInaugurable($projet_inaugurable);
        $oProject->setDateInauguration($this->dateFormat($date_inauguration));
        //setter step 2
        $oProject->setPdmDateDebutAppelOffre($this->dateFormat($pdm_date_debut_appel_offre));
        $oProject->setPdmDateFinAppelOffre($this->dateFormat($pdm_date_fin_offre));
        $oProject->setPdmDateSignatureContrat($this->dateFormat($pdm_date_signature_contrat));
        $oProject->setPdmDesignation($pdm_designation);
        $oProject->setPdmTiersNif($pdm_tiers_nif);
        $oProject->setPdmDateLancementOs(($pdm_date_lancement_os != null) ? $this->dateFormat($pdm_date_lancement_os) : null);
        $oProject->setPdmDateLancementTravauxPrevu($this->dateFormat($pdm_date_lancement_travaux_prevu));
        $oProject->setPdmDateTravauxReel($this->dateFormat($pdm_date_lancement_travaux_reel));
        $oProject->setPdmDelaiExecutionPrevu($pdm_delai_execution_prevu);
        $oProject->setPdmDateFinPrevu($this->dateFormat($pdm_date_fin_prevu));
        //setter step 3
        $oProject->setRfDateSignatureAutorisationEngagement(($pdm_date_lancement_os != null) ? $this->dateFormat($rf_date_signature_autorisation_engagement) : null);
        $oProject->setRfAutorisationEngagement($rf_autorisation_engagement);
        $oProject->setRfCreditPayementAnneeEnCours($rf_credit_payement_annee_en_cours);
        $oProject->setRfMontantDepensesDecaissessMandate($rf_montant_depenses_decaisees_mandate);
        $oProject->setRfMontantDepensesDecaissessLiquide($rf_montant_depenses_decaisees_liquide);
        $oProject->setRfExerciceBudgetaire($rf_exercice_budgetaire);
        $oProject->setAvancement($avancement);
        $oProject->setObservation($observation);
        $oProject->setSituationActuelleTravaux($situation_actuelle_travaux);
        $this->entityManager->persist($oProject);

        return $oProject;
    }

    /**
     * @param $situation_actuelle_marche
     * @return null|object
     */
    public function manageStatutProjet($situation_actuelle_marche)
    {
        $oStatut = null;
        $aEncours = [ConstantSrv::SITUATION_PROJET_ENCOURS_1, ConstantSrv::SITUATION_PROJET_ENCOURS_2, ConstantSrv::SITUATION_PROJET_ENCOURS_3];
        if ($situation_actuelle_marche != null) {
            $statut = 1;
            $repStatut = $this->entityManager->getRepository(PrmStatutProjet::class);
            $repSituation = $this->entityManager->getRepository(PrmSituationProjet::class);
            $oSituation = $repSituation->findOneBy(array('id' => $situation_actuelle_marche));
            if ($oSituation instanceof PrmSituationProjet) {
                if (in_array($situation_actuelle_marche, $aEncours)) {
                    $oStatut = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_EN_COURS));
                } elseif ($situation_actuelle_marche == ConstantSrv::SITUATION_PROJET_TERMINE) {
                    $oStatut = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_TERMINE));
                } else {
                    $oStatut = $repStatut->findOneBy(array('id' => ConstantSrv::STATUT_PROJET_A_FAIRE));
                }
            }
        }
        return $oStatut;
    }

    /**
     * @param $oProjetOld
     * @param $oProjetNew
     * @param $user
     */
    public function observationCheck($obsOld = null, $oProjetNew, $user)
    {
        try {
            if (($oProjetNew instanceof PrmProjet) && ($user instanceof User)) {
                if ($obsOld != $oProjetNew->getObservation()) {
                    $oObs = new PrmObservationProjet();
                    $oObs->setDateUpdate($this->dateNow());
                    $oObs->setUser($user);
                    $oObs->setProjet($oProjetNew);
                    $oObs->setOldVal($obsOld);
                    $oObs->setNewVal($oProjetNew->getObservation());
                    $this->entityManager->persist($oObs);
                    $this->entityManager->flush();
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $photos
     * @param $date
     * @param PrmPhotos $oPhotos
     * @return PrmPhotos
     */
    public function addPhotos($photos, $date, PrmPhotos $oPhotos, $path)
    {
        try {
            $oPhotos->setNom((isset($photos['nom'])) ? $photos['nom'] : null);
            $oPhotos->setChemin($path);
            $oPhotos->setDescription((isset($photos['description'])) ? $photos['description'] : null);
            $oPhotos->setUploadDate($date);
            $oPhotos->setStatut((isset($photos['statut'])) ? $photos['statut'] : null);
            $oPhotos->setMimetype((isset($photos['mimetype'])) ? $photos['mimetype'] : null);
            return $oPhotos;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $photos
     * @param $date
     * @param PrmDocuments $oDoc
     * @return PrmDocuments
     */
    public function addDocuments($doc, $date, PrmDocuments $oDoc, $path)
    {
        try {
            $repType = $this->entityManager->getRepository(PrmDocType::class);
            $oType = (isset($doc['types'])) ? $repType->findOneBy(array('id' => $doc['types'])) : null;
            $oDoc->setNom((isset($doc['nom'])) ? $doc['nom'] : null);
            $oDoc->setChemin($path);
            $oDoc->setDescription((isset($doc['description'])) ? $doc['description'] : null);
            $oDoc->setUploadDate($date);
            $oDoc->setStatut((isset($doc['statut'])) ? $doc['statut'] : null);
            $oDoc->setMimetype((isset($doc['mimetype'])) ? $doc['mimetype'] : null);
            $oDoc->setType(($oType instanceof PrmDocType) ? $oType : null);
            $oDoc->setEnabled(true);
            return $oDoc;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $date
     * @return \DateTime
     */
    public function dateFormat($date)
    {
        if (($date == null) || ($date == "")) {
            $date = null;
        } else {
            date_default_timezone_set('UTC');
            $date = new \DateTime($date);
        }
        return $date;
    }

    /**
     * @return \DateTime|false
     */
    public static function dateNow()
    {
        date_default_timezone_set('UTC');
        $date = new \DateTime();
        //$date = date_modify($date, "+3 hour");
        return $date;
    }

    /**
     * @param $categorie
     * @return PrmCategorieProjet|null|object
     */
    public function checkCategorieProjet($categorie, $api = false)
    {
        try {
            $oCategorie = null;
            $rep = $this->entityManager->getRepository(PrmCategorieProjet::class);
            if (isset($categorie) && ($categorie != null)) {
                $oCat = $rep->findOneBy(array('id' => $categorie['id']));
                if ($oCat instanceof PrmCategorieProjet) {
                    $oCat->setCode($categorie['libelle']);
                    $oCat->setLibelle($categorie['libelle']);
                    $oCategorie = $oCat;
                } else {
                    $oCategorie = new PrmCategorieProjet();
                    $oCategorie->setCode($categorie['libelle']);
                    $oCategorie->setLibelle($categorie['libelle']);
                }
                $this->entityManager->persist($oCategorie);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        if ($api == false) {
            return $oCategorie;
        }
    }

    /**
     * @param $secteur
     * @return PrmSecteur|null|object
     */
    public function checkSecteurProjet($secteur, $api = false)
    {
        try {
            $oObj = null;
            $rep = $this->entityManager->getRepository(PrmSecteur::class);
            if (isset($secteur) && ($secteur != null)) {
                $oObj = $rep->findOneBy(array('id' => $secteur['id']));
                if ($oObj instanceof PrmSecteur) {
                    $oObj->setLibelle($secteur['libelle']);
                } else {
                    $oObj = new PrmSecteur();
                    $oObj->setLibelle($secteur['libelle']);
                }
                $this->entityManager->persist($oObj);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        if ($api == false) {
            return $oObj;
        }
    }

    /**
     * @param $engagement
     * @return PrmSecteur|null|object
     */
    public function checkEngagementProjet($engagement, $api = false)
    {
        try {
            $oObj = null;
            $rep = $this->entityManager->getRepository(PrmEngagement::class);
            if (isset($engagement) && ($engagement != null)) {
                $oObj = $rep->findOneBy(array('id' => $engagement['id']));
                if ($oObj instanceof PrmEngagement) {
                    $oObj->setLibelle($engagement['libelle']);
                } else {
                    $oObj = new PrmEngagement();
                    $oObj->setLibelle($engagement['libelle']);
                }
                $this->entityManager->persist($oObj);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        if ($api == false) {
            return $oObj;
        }
    }

    /**
     * @param $priorite
     * @return PrmSecteur|null|object
     */
    public function checkPrioriteProjet($priorite, $api = false)
    {
        try {
            $oObj = null;
            $rep = $this->entityManager->getRepository(PrmPrioriteProjet::class);
            if (isset($priorite) && ($priorite != null)) {
                $oObj = $rep->findOneBy(array('id' => $priorite['id']));
                if ($oObj instanceof PrmPrioriteProjet) {
                    $oObj->setLibelle($priorite['libelle']);
                } else {
                    $oObj = new PrmPrioriteProjet();
                    $oObj->setLibelle($priorite['libelle']);
                }
                $this->entityManager->persist($oObj);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        if ($api == false) {
            return $oObj;
        }
    }


    /**
     * @param $pdmTm
     * @return PrmTitulaireMarcher|null|object
     */
    public function checkTitulaireMarcherProjet($pdmTm)
    {
        try {
            $oPdmTm = null;
            $rep = $this->entityManager->getRepository(PrmTitulaireMarcher::class);
            if (isset($pdmTm) && ($pdmTm != null)) {
                $oTm = $rep->findOneBy(array('id' => $pdmTm['id']));
                if ($oTm instanceof PrmTitulaireMarcher) {
                    $oTm->setNom($pdmTm['nom']);
                    $oTm->setContact($pdmTm['contact']);
                    $oPdmTm = $oTm;
                } else {
                    $oPdmTm = new PrmTitulaireMarcher();
                    $oPdmTm->setNom($pdmTm['nom']);
                    $oPdmTm->setContact($pdmTm['contact']);
                }
                $this->entityManager->persist($oPdmTm);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        return $oPdmTm;
    }

    /**
     * @param $foundPosts
     * @return JsonResponse
     */
    public function listTypeZone()
    {
        $response = new JsonResponse();
        $rep = $this->entityManager->getRepository(PrmTypeZone::class);
        $oTypeZone = $rep->findAll();
        $context = SerializationContext::create()->setGroups('list_type_zone');
        $context->setSerializeNull(true);
        $serializer = SerializerBuilder::create()->build();
        $results = json_decode($serializer->serialize($oTypeZone, 'json', $context));
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $results);
        $response->setData($data);
        return $response;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function listZone(ParamFetcher $paramFetcher)
    {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', 0);
        $response = new JsonResponse();
        $type = $paramFetcher->get('type_zone');
        $rep = $this->entityManager->getRepository(PrmTypeZone::class);
        $repZone = $this->entityManager->getRepository(PrmZoneGeo::class);
        $oTypeZone = $rep->findOneBy(array('id' => $type));
        if ($oTypeZone instanceof PrmTypeZone) {
            $oAllZone = $repZone->findBy(array('type' => $oTypeZone));
            $context = SerializationContext::create()->setGroups('list_zone');
            $context->setSerializeNull(true);
            $serializer = SerializerBuilder::create()->build();
            $results = json_decode($serializer->serialize($oAllZone, 'json', $context));
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $results);
        } else {
            $data = array('code' => ConstantSrv::CODE_DATA_NOTFOUND, 'Message' => $this->trans->trans('type_not_found'));
        }
        $response->setData($data);
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listSecteur($function = false)
    {
        $rep = $this->entityManager->getRepository(PrmSecteur::class);
        $oAllSecteur = $rep->getAllSecteur();
        $response = $this->sendData($oAllSecteur, 'secteur', $function);
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listEngagement($function = false)
    {
        $rep = $this->entityManager->getRepository(PrmEngagement::class);
        $oAllEngagement = $rep->getAllEngagements();
        $response = $this->sendData($oAllEngagement, 'engagement', $function);
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listTypeProjet()
    {
        $rep = $this->entityManager->getRepository(PrmTypeProjet::class);
        $oAll = $rep->findAll();
        $response = $this->sendData($oAll, 'type_projet');
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listDocType()
    {
        $rep = $this->entityManager->getRepository(PrmDocType::class);
        $oAllEngagement = $rep->findAll();
        $response = $this->sendData($oAllEngagement, 'type_doc');
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listCategorie($function = false)
    {
        $rep = $this->entityManager->getRepository(PrmCategorieProjet::class);
        $oAllCat = $rep->getAllCategorieProjet();
        $response = $this->sendData($oAllCat, 'categorie', $function);
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listTitulaireMarcher()
    {
        $rep = $this->entityManager->getRepository(PrmTitulaireMarcher::class);
        $oAll = $rep->findAll();
        $response = $this->sendData($oAll, 'titulaire');
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listSituationProjet()
    {
        $rep = $this->entityManager->getRepository(PrmSituationProjet::class);
        $oAllSituation = $rep->findAll();
        $response = $this->sendData($oAllSituation, 'situation');
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listPrioriteProjet($function = false)
    {
        $rep = $this->entityManager->getRepository(PrmPrioriteProjet::class);
        $oAllPriorite = $rep->getAllPrioriteProjet();
        $response = $this->sendData($oAllPriorite, 'priorite', $function);
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listStatutProjet()
    {
        $rep = $this->entityManager->getRepository(PrmStatutProjet::class);
        $oAll = $rep->getAllPStatus();
        $response = $this->sendData($oAll, 'statut');
        return $response;
    }

    /**
     * @return JsonResponse
     */
    public function listTypeAdministration()
    {
        $rep = $this->entityManager->getRepository(PrmTypeAdmin::class);
        $oAll = $rep->findAll();
        $response = $this->sendData($oAll, 'type_admin');
        return $response;
    }


    /**
     * @param $oAll
     * @param $field
     * @return JsonResponse
     */
    public function sendData($oAll, $field, $api = false)
    {
        $response = new JsonResponse();
        $context = SerializationContext::create()->setGroups($field);
        $context->setSerializeNull(true);
        $serializer = SerializerBuilder::create()->build();
        $results = json_decode($serializer->serialize($oAll, 'json', $context));
        if ($api == false) {
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $results);
            $response->setData($data);
            return $response;
        } else {
            return $results;
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function uploadMultypleFile(ParamFetcher $paramFetcher)
    {
        $response = new JsonResponse();
        $files = $paramFetcher->get('file');
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                $path = $this->container->getParameter('import_path');
                $name = $this->nameUpload($file['name']);
                $this->uploadFile($name, $file['value'], $path);
            }
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'));
        $response->setData($data);
        return $response;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function uploadPhotos(ParamFetcher $paramFetcher)
    {
        $response = new JsonResponse();
        $name = $paramFetcher->get('name');
        $value = $paramFetcher->get('value');
        $path = $this->container->getParameter('import_path_photos');
        $name = $this->nameUpload($name);
        $this->uploadFile($name, $value, $path);
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'));
        $response->setData($data);
        return $response;
    }


    /**
     * @param $name
     * @param $value
     * @param $fileDirectoryPath
     */
    public function uploadFile($name, $value, $fileDirectoryPath)
    {
        try {
            if (!is_dir($fileDirectoryPath)) {
                @mkdir($fileDirectoryPath, 0777, true);
            }
            $filePath = $fileDirectoryPath . $name;
            $this->serviceFileUpload->base64ToFile($value, $filePath);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $name
     * @return string
     */
    public function nameUpload($name)
    {
        $now = $this->dateNow()->format('Ymd_Hisu');
        $name = $now . '_' . $name;
        return $name;
    }

    /**
     * @param $fileId
     * @return BinaryFileResponse|JsonResponse
     */
    public function downloadFile($fileId)
    {
        try {
            $file = $this->entityManager->getRepository(PrmPhotos::class)->find($fileId);
            if (!$file) {
                $array = array(
                    'status' => ConstantSrv::CODE_DATA_NOTFOUND,
                    'message' => $this->trans->trans('file_not_found')
                );
                $response = new JsonResponse ($array, 200);
                return $response;
            }
            $fileName = $file->getNom();
            $dirFile = $file->getChemin();
            $filePath = $dirFile . $fileName;
            $response = new BinaryFileResponse ($filePath);
            $expFileName = explode('.', $fileName);
            $extension = end($expFileName);
            //$response->headers->set('Content-Type', 'image/$extension');
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, "sql_calc_node_zone.sql");
        } catch (\Exception $e) {
            $array = array(
                'status' => ConstantSrv::CODE_BAD_REQUEST,
                'message' => $this->trans->trans('download_file_error') . ': ' . $e->getMessage(),
            );
            $response = new JsonResponse ($array, 400);
        }
        return $response;
    }

    /**
     * @param $action
     * @param $string
     * @return bool|string
     */
    public function decryptEncrypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'prm secret key';
        $secret_iv = 'prm secret iv';
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        /*dump($output);
        die;*/
        return $output;
    }

    /**
     * @return JsonResponse
     */
    public function getListProjetParent(User $user)
    {
        $response = new JsonResponse();
        $oAll = [];
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
        $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;
        if (($administration != null) || ($region != null)) {
            $oAll = $rep->getAllProjetParent($administration, $region);
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $oAll);
        $response->setData($data);
        return $response;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param User $user
     * @param bool $parent
     * @return JsonResponse
     */
    public function getListProjet(ParamFetcher $paramFetcher, User $user, $parent = false)
    {
        $response = new JsonResponse();
        $userProjet = [];
        $result = ['list' => [], 'total' => 0];
        $data['nom'] = $paramFetcher->get('nom');
        $data['zone'] = $paramFetcher->get('localite_emplacement');
        $all = $paramFetcher->get('all');
        $data['type_zone'] = $paramFetcher->get('type_zone');
        $data['statut'] = $paramFetcher->get('statut');
        $data['en_retard'] = $paramFetcher->get('en_retard');
        $data['projet_inaugurable'] = $paramFetcher->get('projet_inaugurable');
        $data['pdm_date_fin_prevu_debut'] = $paramFetcher->get('pdm_date_fin_prevu_debut');
        $data['pdm_date_fin_prevu_fin'] = $paramFetcher->get('pdm_date_fin_prevu_fin');
        $data['type_administration'] = $paramFetcher->get('type_administration');
        $data['administration'] = $paramFetcher->get('administration');
        $data['engagement'] = $paramFetcher->get('engagement');
        $data['secteur'] = $paramFetcher->get('secteur');
        $data['conv_cl'] = $paramFetcher->get('conv_cl');
        $data['id_user'] = $user->getId();
        $data['page'] = $paramFetcher->get('page');
        $data['limit'] = $paramFetcher->get('itemsPerPage');
        $data['geo_ref'] = $paramFetcher->get('geo_ref');
        $data['projet_parent'] = $paramFetcher->get('projet_parent');
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
        $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;
        if (($administration != null) || ($region != null)) {
            $aAll = $rep->getAllProjet($data, $all, $administration, $region, $user);
            if (isset($aAll['list']) && !empty($aAll['list'])) {
                if ($parent == true) {
                    $aAll['list'] = $this->manageProjetParentById($aAll['list']);
                }
                foreach ($aAll['list'] as $Prmojet) {
                    $Prmojet = (isset($Prmojet['id']) && is_numeric($Prmojet['id'])) ? $Prmojet['id'] : $Prmojet;
                    $projet = $rep->getProjetOneByOne($Prmojet);
                    array_push($result['list'], $projet);
                }
                $result['total'] = $aAll['total'];
                $result = $this->formatMultypleProject($result, $data['geo_ref'], true, $parent, $user);
            }
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
        $response->setData($data);
        return $response;
    }

    /**
     * @param $aIdProjet
     * @return array
     */
    public function manageProjetParentById($aIdProjet)
    {
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $aProjetParent = [];
        $aSousProjet = [];
        $aSp = [];
        foreach ($aIdProjet as $projet) {
            ($projet['projet_id'] == null) ? array_push($aProjetParent, $projet['id']) : array_push($aSousProjet, $projet['projet_id']);
        }
        if (!empty($aSousProjet)) {
            $aSousProjet = $rep->getProjetParentById($aSousProjet);
            foreach ($aSousProjet as $sp) {
                array_push($aSp, $sp['id']);
            }
        }
        $aProjetParent = array_merge($aProjetParent, $aSp);
        return $aProjetParent;
    }


    /**
     * @param $idZone
     * @param $typeZone
     * @return mixed
     */
    public function zoneRequest($idZone, $typeZone)
    {
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        if ($typeZone != $this->container->getParameter('zone_projet')) {
            $aIdProjet = $rep->getProjetInNode($idZone);
        } else {
            $aIdProjet = $rep->getProjetInLeaf($idZone);
        }
        return $aIdProjet;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param User $user
     * @return JsonResponse
     */
    public function getProjetById(ParamFetcher $paramFetcher, User $user)
    {
        $response = new JsonResponse();
        $idProjet = $paramFetcher->get('projet_id');
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $aAll = $rep->getProjetById($idProjet);
        if (is_array($aAll) && !empty($aAll)) {
            $aAll = $this->formatOneProject($aAll);
            $aPhotos = $this->getDocOrPhotos($idProjet, false);
            $aAll['localite_emplacement'] = $this->getZoneByProjet($idProjet);
            $aAll['photos'] = (!empty($aPhotos) && isset($aPhotos[0])) ? [$aPhotos[0]] : [];
            $aAll['document'] = $this->getDocOrPhotos($idProjet, true);
            $aAll['profil_affecter'] = $this->getProfilAffecter($idProjet, $user);
        } else {
            $aAll = [];
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $aAll);
        $response->setData($data);
        return $response;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @param User $user
     * @return JsonResponse
     */
    public function getProjetByIdMinInfo(ParamFetcher $paramFetcher, User $user)
    {
        $response = new JsonResponse();
        $idProjet = $paramFetcher->get('projet_id');
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $aAll = $rep->getProjetByIdMinInfo($idProjet);
        if (is_array($aAll) && !empty($aAll)) {
            $aPhotos = $this->getDocOrPhotos($idProjet, false);
            $aAll['photos'] = (!empty($aPhotos) && isset($aPhotos[0])) ? [$aPhotos[0]] : [];
        } else {
            $aAll = [];
        }
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $aAll);
        $response->setData($data);
        return $response;
    }

    /**
     * @param $idProjet
     * @param User $user
     * @return array
     */
    public function getProfilAffecter($idProjet, User $user)
    {
        $result = [];
        $idProfil = $user->getProfil()->getId();
        if (!empty($idProjet) && !empty($idProfil)) {
            $rep = $this->entityManager->getRepository(PrmProjet::class);
            $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
            $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;
            if (($administration == null) && ($region == null)) {
                return $result;
            }
            $data = $rep->getProfilAffecter($idProjet, ConstantSrv::STATUT_PROJET_TERMINE, $administration, $region);
            foreach ($data as $profil) {
                array_push($result, $profil['id']);
            }
        }
        return $result;
    }

    /**
     * @param $idProjet
     */
    public function getZoneByProjet($idProjet, $georef = false)
    {
        $data = [];
        if (!empty($idProjet)) {
            $rep = $this->entityManager->getRepository(PrmProjet::class);
            $data = $rep->getZoneByProjet($idProjet, $georef);
        }
        return $data;
    }

    /**
     * @param $data
     * @return array
     */
    public function formatMultypleProject($data, $geo_ref = false, $zoneInfo = true, $parent = false, User $user)
    {
        try {
            $result = ['list' => [], 'totalItem' => 0];
            $rep = $this->entityManager->getRepository(PrmProjet::class);
            if (is_array($data) && !empty($data)) {
                ($parent == true) ? $aProjetUser = $this->getAllIdProjectUser($user) : $aProjetUser = [];
                foreach ($data['list'] as $aProject) {
                    (($parent == true) && !empty($aProjetUser)) ? $aProject['proprietaire'] = (in_array($aProject['id'], $aProjetUser) ? true : false) : null;
                    $aPhotos = $this->getDocOrPhotos($aProject['id'], false);
                    $aAllProjet = $rep->findOneBy(array('projet' => $aProject['id']));
                    $aProject['fils'] = ($aAllProjet != null) ? true : false;
                    $aProject['coordonnegps'] = json_decode($aProject['coordonnegps']);
                    $aProject['photos'] = (!empty($aPhotos) && isset($aPhotos[0])) ? [$aPhotos[0]] : [];
                    ($zoneInfo == true) ? $aProject['localite_emplacement'] = $this->getZoneByProjet($aProject['id'], $geo_ref) : null;
                    array_push($result['list'], $aProject);
                }
                $result['totalItem'] = (isset($data['total'])) ? $data['total'] : null;
            }
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $aProject
     * @return mixed
     */
    public function formatOneProject($aProject)
    {
        try {
            $aProject['categorie'] = [
                "id" => $aProject['categorie'],
                "libelle" => $aProject['categorie_libelle']
            ];
            $aProject['coordonnegps'] = json_decode($aProject['coordonnegps']);
            return $aProject;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function getFileByIdProjet(ParamFetcher $paramFetcher)
    {
        $response = new JsonResponse();
        $idProjet = $paramFetcher->get('projet_id');
        $doc = $paramFetcher->get('doc');
        $file = $this->getDocOrPhotos($idProjet, $doc);
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $file);
        $response->setData($data);
        return $response;
    }

    /**
     * @param $idProjet
     * @param $doc
     * @return array|object[]
     */
    public function getDocOrPhotos($idProjet, $doc)
    {
        if ($doc == true) {
            $file = $this->entityManager->getRepository(PrmDocuments::class)->findBy(array('projet' => $idProjet, 'enabled' => true));
        } else {
            $file = $this->entityManager->getRepository(PrmPhotos::class)->findBy(array('projet' => $idProjet));
        }
        $file = (!empty($file)) ? $this->formatDocData($file) : [];
        return $file;
    }

    /**
     * @param $data
     * @return array
     */
    public function formatDocData($data)
    {
        $result = [];
        if (is_array($data) && !empty($data)) {
            foreach ($data as $ofile) {
                if (($ofile instanceof PrmDocuments) || ($ofile instanceof PrmPhotos)) {
                    $file = [];
                    $key = "SlAsH_";
                    $file['id'] = $ofile->getId();
                    $file['chemin'] = $ofile->getChemin();
                    $file['nom'] = $ofile->getNom();
                    $file['chemin_complet'] = $ofile->getChemin() . $ofile->getNom();
                    $file['upload_date'] = $ofile->getUploadDate();
                    $file['description'] = $ofile->getDescription();
                    $file['statut'] = $ofile->getStatut();
                    $fileBase64 = $this->serviceFileUpload->fileToBase64($file['chemin_complet']);
                    $file['mimetype'] = $ofile->getMimetype();
                    $file['value'] = $fileBase64;
                    array_push($result, $file);
                }
            }
        }
        return $result;
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function listObsByProjetId(ParamFetcher $paramFetcher)
    {
        $response = new JsonResponse();
        $idProjet = $paramFetcher->get('projet_id');
        $response = new JsonResponse();
        $rep = $this->entityManager->getRepository(PrmObservationProjet::class);
        $oTypeZone = $rep->findBy(array('projet' => $idProjet));
        $context = SerializationContext::create()->setGroups('observation:read');
        $context->setSerializeNull(true);
        $serializer = SerializerBuilder::create()->build();
        $results = json_decode($serializer->serialize($oTypeZone, 'json', $context));
        $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $results);
        $response->setData($data);
        return $response;
    }

    /**
     * @param OutputInterface $output
     */
    public function checkProjetEnRetard(OutputInterface $output)
    {
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $oProjets = $rep->findAll();
        if (!empty($oProjets)) {
            foreach ($oProjets as $oProjet) {
                if ($oProjet instanceof PrmProjet) {
                    if (($oProjet->getPdmDateFinPrevu() < $this->dateNow()) && ($oProjet->getStatut()->getId() != ConstantSrv::STATUT_PROJET_TERMINE)) {
                        $oProjet->setEnRetard(true);
                        $this->entityManager->persist($oProjet);
                        $output->writeln('<comment>- Projet¨en retard id: ' . $oProjet->getId() . ' -> Nom: ' . $oProjet->getNom() . ' </comment>');
                    }
                }
            }
            $this->entityManager->flush();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public function saveCategorie(ParamFetcher $paramFetcher)
    {
        try {
            $response = new JsonResponse();
            $aCategorie = $paramFetcher->get('categorie');
            if (is_array($aCategorie) && !empty($aCategorie)) {
                foreach ($aCategorie as $categorie) {
                    $this->checkCategorieProjet($categorie, true);
                }
            }
            $result = $this->listCategorie(true);
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public
    function saveSecteur(ParamFetcher $paramFetcher)
    {
        try {
            $response = new JsonResponse();
            $aSecteur = $paramFetcher->get('secteur');
            if (is_array($aSecteur) && !empty($aSecteur)) {
                foreach ($aSecteur as $secteur) {
                    $this->checkSecteurProjet($secteur, true);
                }
            }
            $result = $this->listSecteur(true);
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public
    function saveEngagement(ParamFetcher $paramFetcher)
    {
        try {
            $response = new JsonResponse();
            $aEngagement = $paramFetcher->get('engagement');
            if (is_array($aEngagement) && !empty($aEngagement)) {
                foreach ($aEngagement as $engagement) {
                    $this->checkEngagementProjet($engagement, true);
                }
            }
            $result = $this->listEngagement(true);
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param ParamFetcher $paramFetcher
     * @return JsonResponse
     */
    public
    function savePriorite(ParamFetcher $paramFetcher)
    {
        try {
            $response = new JsonResponse();
            $aPriorite = $paramFetcher->get('priorite');
            if (is_array($aPriorite) && !empty($aPriorite)) {
                foreach ($aPriorite as $priorite) {
                    $this->checkPrioriteProjet($priorite, true);
                }
            }
            $result = $this->listPrioriteProjet(true);
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param User $user
     * @return JsonResponse
     */
    public function getListProjetDashboard(User $user)
    {
        $response = new JsonResponse();
        $result = ['list' => [], 'total' => 0];
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
        $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;
        if (($administration == null) && ($region == null)) {
            return $result;
        }
        if (($administration != null) || ($region != null)) {
            $aAll = $rep->getAllProjetInDashboard($administration, $region);
            if (isset($aAll) && !empty($aAll)) {
                foreach ($aAll as $Projet) {
                    $infoProjet = $rep->getProjetOneByOne($Projet['id']);
                    array_push($result['list'], $infoProjet);
                }
                $result['total'] = count($aAll);
                $result = $this->formatMultypleProject($result, false, false, false, $user);
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
    public function getListSousProjet(ParamFetcher $paramFetcher, User $user)
    {
        try {
            $response = new JsonResponse();
            $result = ['list' => []];
            $rep = $this->entityManager->getRepository(PrmProjet::class);
            $idProjet = $paramFetcher->get('projet_id');
            $geoRef = $paramFetcher->get('geo_ref');
            $aAll = $rep->listSousProjet($idProjet);
            if (isset($aAll) && !empty($aAll)) {
                foreach ($aAll as $Prmojet) {
                    $projet = $rep->getProjetOneByOne($Prmojet['id']);
                    array_push($result['list'], $projet);
                }
                $result = $this->formatMultypleProject($result, $geoRef, true, true, $user);
            }
            $data = array('code' => ConstantSrv::CODE_SUCCESS, 'Message' => $this->trans->trans('success_request'), 'data' => $result);
            $response->setData($data);
            return $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param User $user
     * @return array
     */
    public function getAllIdProjectUser(User $user)
    {
        $result = [];
        $rep = $this->entityManager->getRepository(PrmProjet::class);
        $administration = ($user->getAdministration() instanceof PrmAdministration) ? $user->getAdministration()->getId() : null;
        $region = ($user->getRegion() instanceof PrmZoneGeo) ? $user->getRegion()->getId() : null;
        if (($administration == null) && ($region == null)) {
            return $result;
        }
        $allIdProjet = $rep->getAllIdProjectUser($user->getId(), true, $administration, $region);
        foreach ($allIdProjet as $Projet) {
            array_push($result, $Projet['id']);
        }
        return $result;
    }
}