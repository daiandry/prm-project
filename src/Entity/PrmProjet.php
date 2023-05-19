<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Annotation\TrackableClass;
use App\Repository\PrmProjetRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use App\Controller\ListTachesProjet;
use App\Controller\HistoriqueAvancementProjet;
use App\Controller\KpiProjet;
/**
 * @TrackableClass()
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     normalizationContext={"groups"={"projet-taches"}},
 *     itemOperations={
 *          "get",
 *          "get_liste_taches"={
 *              "name"="api_projet_list_taches",
 *              "method"="GET",
 *              "path"="/projet/{id}/taches",
 *              "controller"=ListTachesProjet::class,
 *              "normalization_context"={"groups"={"projet-taches"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Liste de taches d'un projet",
 *                  "parameters"={
 *                      {
 *                          "name" = "id",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      },{
 *                          "name" = "page",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      },{
 *                          "name" = "itemsPerPage",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      }
 *                  }
 *              }
 *          },
 *          "historique_avancement_projet"={
 *              "name"="api_projet_historique_avancement",
 *              "method"="GET",
 *              "path"="/projet/{id}/historique-avancement",
 *              "controller"=HistoriqueAvancementProjet::class,
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Liste de taches d'un projet",
 *                  "parameters"={
 *                      {
 *                          "name" = "id",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      }
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get_projet_kpi"={
 *              "name"="api_projet_kpi",
 *              "method"="GET",
 *              "path"="/projet/kpi",
 *              "controller"=KpiProjet::class,
 *              "normalization_context"={"groups"={"projet-kpi"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Liste de taches d'un projet",
 *                  "parameters"={
 *                      {
 *                          "name" = "id",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      },{
 *                          "name" = "typeAdmin",
 *                          "in" = "query",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "administration",
 *                          "in" = "query",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "statut",
 *                          "in" = "query",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "dateFinPrev",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "secteur",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "engagement",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "region",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "enRetard",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "inaugurable",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },{
 *                          "name" = "nomProjet",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      },
 *                  }
 *              }
 *          }
 *
 *     }
 * )
 * @ORM\Entity(repositoryClass=PrmProjetRepository::class)
 */
class PrmProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"projet_parent", "projet-taches","observation:read"})
     * @Groups({"projet-taches","log:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"create"})
     * @JMS\Groups({"projet_parent", "projet-taches","mail:inaugurable","observation:read"})
     * @Groups({"projet-taches","log:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"create"})
     */
    private $conv_cl;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"create"})
     */
    private $description;

    /**
     * @var PrmProjet
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProjet")
     */
    private $projet;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmEngagement",inversedBy="PrmpProjet",cascade={"persist"})
     * @JMS\Groups({"mail:inaugurable"})
     */
    private $engagement;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_debut_appel_offre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_fin_appel_offre;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_signature_contrat;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmTitulaireMarcher",inversedBy="projet",cascade={"persist"})
     * @JMS\Groups({"mail:inaugurable"})
     */
    private $pdm_titulaire_du_marche;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pdm_designation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pdm_tiers_nif;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_lancement_os;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_lancement_travaux_prevu;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_travaux_reel;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $pdm_delai_execution_prevu;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pdm_date_fin_prevu;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $rf_date_signature_autorisation_engagement;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rf_autorisation_engagement;

    /**
     * @ORM\Column(type="float", length=255, nullable=true)
     */
    private $rf_credit_payement_annee_en_cours;

    /**
     * @ORM\Column(type="float", length=255, nullable=true)
     */
    private $rf_montant_depenses_decaissess_mandate;

    /**
     * @ORM\Column(type="float", length=255, nullable=true)
     */
    private $rf_montant_depenses_decaissess_liquide;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rf_exercice_budgetaire;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rf_budget_consomme;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmSituationProjet",inversedBy="projet",cascade={"persist"})
     */
    private $situation_actuelle_marche;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"log:read"})
     */
    private $observation;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmPrioriteProjet",inversedBy="projet",cascade={"persist"})
     */
    private $priorite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $soa_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pcop_compte;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmCategorieProjet",inversedBy="projet",cascade={"persist"})
     */
    private $categorie;

    /**
     * @var PrmZoneGeo
     * @ORM\ManyToMany(targetEntity="App\Entity\PrmZoneGeo",inversedBy="projet", cascade={"remove"})
     * @ORM\JoinTable(
     *  name="prm_projet_zone",
     *  joinColumns={
     *      @ORM\JoinColumn(name="projet_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="zone_id", referencedColumnName="id")
     *  }
     * )
     * @JMS\Groups({"mail:inaugurable"})
     */
    private $zone;

    /**
     * @var string
     *
     * @ORM\Column(name="coordonneGPS", type="text", length=155, nullable=true)
     *
     */
    private $coordonneGPS;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $promesse_presidentielle;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $inaugurable;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $mailInaugurable;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $mailAchever;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $en_retard;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_inauguration;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="projet",cascade={"persist"})
     * @JMS\Groups({"mail:inaugurable"})
     */
    private $created_by;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmSecteur",inversedBy="PrmProjet",cascade={"persist"})
     * @JMS\Groups({"mail:inaugurable"})
     */
    private $secteur;

    /**
     * @var PrmPhotos
     * @ORM\OneToMany(targetEntity="App\Entity\PrmPhotos",mappedBy="projet",cascade={"persist"})
     */
    private $photos;

    /**
     * @var PrmDocuments
     * @ORM\OneToMany(targetEntity="App\Entity\PrmDocuments",mappedBy="projet",cascade={"persist"})
     */
    private $doc;

    /**
     * @var PrmTypeProjet
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmTypeProjet",inversedBy="typeProjet",cascade={"persist"})
     */
    private $type;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_create;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_modify;

    /**
     * @var PrmProfil
     * @ORM\ManyToMany(targetEntity="App\Entity\PrmProfil",inversedBy="projet", cascade={"remove"})
     * @ORM\JoinTable(
     *  name="prm_projet_profil",
     *  joinColumns={
     *      @ORM\JoinColumn(name="projet_id", referencedColumnName="id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="profil_id", referencedColumnName="id")
     *  }
     * )
     * @JMS\Groups({"affectation"})
     */
    private $profil;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $situation_actuelle_travaux;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"create"})
     */
    private $avancement;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmStatutProjet",inversedBy="projet",cascade={"persist"})
     */
    private $statut;

    /**
     * @ORM\OneToMany(targetEntity=PrmTaches::class, mappedBy="projet")
     * @Groups({"projet-taches"})
     */
    private $taches;

    /**
     * @var PrmPhotos
     * @ORM\OneToMany(targetEntity="App\Entity\PrmHistoriqueAvancement",mappedBy="projet",cascade={"persist"})
     */
    private $historique_avanvement;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     */
    private $valide;

    /**
     * @return PrmDocuments
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @return PrmProfil
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * @param PrmProfil $profil
     */
    public function setProfil($profil)
    {
        $this->profil = $profil;
    }


    /**
     * @param PrmDocuments $doc
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getDateCreate()
    {
        return $this->date_create;
    }

    /**
     * @return PrmPhotos
     */
    public function getHistoriqueAvanvement()
    {
        return $this->historique_avanvement;
    }

    /**
     * @param PrmPhotos $historique_avanvement
     */
    public function setHistoriqueAvanvement($historique_avanvement)
    {
        $this->historique_avanvement = $historique_avanvement;
    }

    /**
     * @return mixed
     */
    public function getValide()
    {
        return $this->valide;
    }

    /**
     * @param mixed $valide
     */
    public function setValide($valide)
    {
        $this->valide = $valide;
    }

    /**
     * @param mixed $date_create
     */
    public function setDateCreate($date_create)
    {
        $this->date_create = $date_create;
    }

    /**
     * @return mixed
     */
    public function getDateModify()
    {
        return $this->date_modify;
    }

    /**
     * @param mixed $date_modify
     */
    public function setDateModify($date_modify)
    {
        $this->date_modify = $date_modify;
    }

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->date_create = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getConvCl()
    {
        return $this->conv_cl;
    }

    /**
     * @return mixed
     */
    public function getAvancement()
    {
        return $this->avancement;
    }

    /**
     * @param mixed $avancement
     */
    public function setAvancement($avancement)
    {
        $this->avancement = $avancement;
    }

    /**
     * @return mixed
     */
    public function getEngagement()
    {
        return $this->engagement;
    }

    /**
     * @param mixed $engagement
     */
    public function setEngagement($engagement)
    {
        $this->engagement = $engagement;
    }

    public function setConvCl($conv_cl): self
    {
        $this->conv_cl = $conv_cl;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRfBudgetConsomme()
    {
        return $this->rf_budget_consomme;
    }

    /**
     * @param mixed $rf_budget_consomme
     */
    public function setRfBudgetConsomme($rf_budget_consomme)
    {
        $this->rf_budget_consomme = $rf_budget_consomme;
    }

    /**
     * @return PrmProjet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * @param PrmProjet $projet
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;
    }

    public function getPdmDateFinAppelOffre()
    {
        return $this->pdm_date_fin_appel_offre;
    }

    public function setPdmDateFinAppelOffre(DateTimeInterface $pdm_date_fin_appel_offre)
    {
        $this->pdm_date_fin_appel_offre = $pdm_date_fin_appel_offre;

        return $this;
    }

    public function getPdmTitulaireDuMarche()
    {
        return $this->pdm_titulaire_du_marche;
    }

    public function setPdmTitulaireDuMarche($pdm_titulaire_du_marche)
    {
        $this->pdm_titulaire_du_marche = $pdm_titulaire_du_marche;

        return $this;
    }

    public function getPdmDesignation()
    {
        return $this->pdm_designation;
    }

    public function setPdmDesignation($pdm_designation)
    {
        $this->pdm_designation = $pdm_designation;

        return $this;
    }

    public function getPdmTiersNif()
    {
        return $this->pdm_tiers_nif;
    }

    public function setPdmTiersNif($pdm_tiers_nif): self
    {
        $this->pdm_tiers_nif = $pdm_tiers_nif;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPdmDateLancementOs()
    {
        return $this->pdm_date_lancement_os;
    }

    /**
     * @param mixed $pdm_date_lancement_os
     */
    public function setPdmDateLancementOs($pdm_date_lancement_os)
    {
        $this->pdm_date_lancement_os = $pdm_date_lancement_os;
    }


    /**
     * @return mixed
     */
    public function getPdmDateLancementTravauxPrevu()
    {
        return $this->pdm_date_lancement_travaux_prevu;
    }

    /**
     * @param mixed $pdm_date_lancement_travaux_prevu
     */
    public function setPdmDateLancementTravauxPrevu($pdm_date_lancement_travaux_prevu)
    {
        $this->pdm_date_lancement_travaux_prevu = $pdm_date_lancement_travaux_prevu;
    }

    /**
     * @return mixed
     */
    public function getPdmDateTravauxReel()
    {
        return $this->pdm_date_travaux_reel;
    }

    /**
     * @param mixed $pdm_date_travaux_reel
     */
    public function setPdmDateTravauxReel($pdm_date_travaux_reel)
    {
        $this->pdm_date_travaux_reel = $pdm_date_travaux_reel;
    }

    /**
     * @return mixed
     */
    public function getPdmDelaiExecutionPrevu()
    {
        return $this->pdm_delai_execution_prevu;
    }

    /**
     * @param mixed $pdm_delai_execution_prevu
     */
    public function setPdmDelaiExecutionPrevu($pdm_delai_execution_prevu)
    {
        $this->pdm_delai_execution_prevu = $pdm_delai_execution_prevu;
    }

    /**
     * @return mixed
     */
    public function getPdmDateFinPrevu()
    {
        return $this->pdm_date_fin_prevu;
    }

    /**
     * @param mixed $pdm_date_fin_prevu
     */
    public function setPdmDateFinPrevu($pdm_date_fin_prevu)
    {
        $this->pdm_date_fin_prevu = $pdm_date_fin_prevu;
    }

    /**
     * @return mixed
     */
    public function getSituationActuelleMarche()
    {
        return $this->situation_actuelle_marche;
    }

    /**
     * @param mixed $situation_actuelle_marche
     */
    public function setSituationActuelleMarche($situation_actuelle_marche)
    {
        $this->situation_actuelle_marche = $situation_actuelle_marche;
    }

    /**
     * @return mixed
     */
    public function getRfDateSignatureAutorisationEngagement()
    {
        return $this->rf_date_signature_autorisation_engagement;
    }

    /**
     * @param mixed $rf_date_signature_autorisation_engagement
     */
    public function setRfDateSignatureAutorisationEngagement($rf_date_signature_autorisation_engagement)
    {
        $this->rf_date_signature_autorisation_engagement = $rf_date_signature_autorisation_engagement;
    }


    public function getObservation()
    {
        return $this->observation;
    }


    public function setObservation($observation)
    {
        $this->observation = $observation;
    }

    /**
     * @return mixed
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * @param mixed $priorite
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;
    }

    /**
     * @return mixed
     */
    public function getSoaCode()
    {
        return $this->soa_code;
    }

    /**
     * @param mixed $soa_code
     */
    public function setSoaCode($soa_code)
    {
        $this->soa_code = $soa_code;
    }

    /**
     * @return mixed
     */
    public function getPcopCompte()
    {
        return $this->pcop_compte;
    }

    /**
     * @param mixed $pcop_compte
     */
    public function setPcopCompte($pcop_compte)
    {
        $this->pcop_compte = $pcop_compte;
    }

    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param mixed $categorie
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }

    /**
     * @return PrmZoneGeo
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param PrmZoneGeo $zone
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * @return string
     */
    public function getCoordonneGPS()
    {
        return $this->coordonneGPS;
    }

    /**
     * @param $coordonneGPS
     */
    public function setCoordonneGPS($coordonneGPS)
    {
        $this->coordonneGPS = $coordonneGPS;
    }

    /**
     * @return mixed
     */
    public function getPromessePresidentielle()
    {
        return $this->promesse_presidentielle;
    }

    /**
     * @param mixed $promesse_presidentielle
     */
    public function setPromessePresidentielle($promesse_presidentielle)
    {
        $this->promesse_presidentielle = $promesse_presidentielle;
    }

    /**
     * @return bool
     */
    public function isInaugurable()
    {
        return $this->inaugurable;
    }

    /**
     * @param bool $inaugurable
     */
    public function setInaugurable($inaugurable)
    {
        $this->inaugurable = $inaugurable;
    }

    /**
     * @return mixed
     */
    public function getEnRetard()
    {
        return $this->en_retard;
    }

    /**
     * @param mixed $en_retard
     */
    public function setEnRetard($en_retard)
    {
        $this->en_retard = $en_retard;
    }

    /**
     * @return mixed
     */
    public function getDateInauguration()
    {
        return $this->date_inauguration;
    }

    /**
     * @param mixed $date_inauguration
     */
    public function setDateInauguration($date_inauguration)
    {
        $this->date_inauguration = $date_inauguration;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getPdmDateDebutAppelOffre()
    {
        return $this->pdm_date_debut_appel_offre;
    }

    /**
     * @param mixed $pdm_date_debut_appel_offre
     */
    public function setPdmDateDebutAppelOffre($pdm_date_debut_appel_offre)
    {
        $this->pdm_date_debut_appel_offre = $pdm_date_debut_appel_offre;
    }

    /**
     * @return mixed
     */
    public function getSecteur()
    {
        return $this->secteur;
    }

    /**
     * @param mixed $secteur
     */
    public function setSecteur($secteur)
    {
        $this->secteur = $secteur;
    }

    /**
     * @return int
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param int $photos
     */
    public function setPhotos($photos)
    {
        $this->photos = $photos;
    }

    /**
     * @return PrmTypeProjet
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param PrmTypeProjet $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getRfCreditPayementAnneeEnCours()
    {
        return $this->rf_credit_payement_annee_en_cours;
    }

    /**
     * @param mixed $rf_credit_payement_annee_en_cours
     */
    public function setRfCreditPayementAnneeEnCours($rf_credit_payement_annee_en_cours)
    {
        $this->rf_credit_payement_annee_en_cours = $rf_credit_payement_annee_en_cours;
    }

    /**
     * @return mixed
     */
    public function getRfMontantDepensesDecaissessMandate()
    {
        return $this->rf_montant_depenses_decaissess_mandate;
    }

    /**
     * @param mixed $rf_montant_depenses_decaissess_mandate
     */
    public function setRfMontantDepensesDecaissessMandate($rf_montant_depenses_decaissess_mandate)
    {
        $this->rf_montant_depenses_decaissess_mandate = $rf_montant_depenses_decaissess_mandate;
    }

    /**
     * @return mixed
     */
    public function getRfMontantDepensesDecaissessLiquide()
    {
        return $this->rf_montant_depenses_decaissess_liquide;
    }

    /**
     * @param mixed $rf_montant_depenses_decaissess_liquide
     */
    public function setRfMontantDepensesDecaissessLiquide($rf_montant_depenses_decaissess_liquide)
    {
        $this->rf_montant_depenses_decaissess_liquide = $rf_montant_depenses_decaissess_liquide;
    }

    /**
     * @return mixed
     */
    public function getRfExerciceBudgetaire()
    {
        return $this->rf_exercice_budgetaire;
    }

    /**
     * @param mixed $rf_exercice_budgetaire
     */
    public function setRfExerciceBudgetaire($rf_exercice_budgetaire)
    {
        $this->rf_exercice_budgetaire = $rf_exercice_budgetaire;
    }

    /**
     * @return Collection|PrmTaches[]
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(PrmTaches $tach): self
    {
        if (!$this->taches->contains($tach)) {
            $this->taches[] = $tach;
            $tach->setProjet($this);
        }

        return $this;
    }

    public function removeTach(PrmTaches $tach): self
    {
        if ($this->taches->contains($tach)) {
            $this->taches->removeElement($tach);
            // set the owning side to null (unless already changed)
            if ($tach->getProjet() === $this) {
                $tach->setProjet(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateDateModify()
    {
        $this->date_modify = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getRfAutorisationEngagement()
    {
        return $this->rf_autorisation_engagement;
    }

    /**
     * @param mixed $rf_autorisation_engagement
     */
    public function setRfAutorisationEngagement($rf_autorisation_engagement)
    {
        $this->rf_autorisation_engagement = $rf_autorisation_engagement;
    }

    /**
     * @return mixed
     */
    public function getPdmDateSignatureContrat()
    {
        return $this->pdm_date_signature_contrat;
    }

    /**
     * @param mixed $pdm_date_signature_contrat
     */
    public function setPdmDateSignatureContrat($pdm_date_signature_contrat)
    {
        $this->pdm_date_signature_contrat = $pdm_date_signature_contrat;
    }

    /**
     * @return bool
     */
    public function isMailInaugurable()
    {
        return $this->mailInaugurable;
    }

    /**
     * @param bool $mailInaugurable
     */
    public function setMailInaugurable($mailInaugurable)
    {
        $this->mailInaugurable = $mailInaugurable;
    }

    /**
     * @return bool
     */
    public function isMailAchever()
    {
        return $this->mailAchever;
    }

    /**
     * @param bool $mailAchever
     */
    public function setMailAchever($mailAchever)
    {
        $this->mailAchever = $mailAchever;
    }

    /**
     * @return mixed
     */
    public function getSituationActuelleTravaux()
    {
        return $this->situation_actuelle_travaux;
    }

    /**
     * @param mixed $situation_actuelle_travaux
     */
    public function setSituationActuelleTravaux($situation_actuelle_travaux)
    {
        $this->situation_actuelle_travaux = $situation_actuelle_travaux;
    }
}
