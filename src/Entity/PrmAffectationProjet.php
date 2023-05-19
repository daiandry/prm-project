<?php

namespace App\Entity;

use App\Repository\PrmAffectationProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmAffectationProjetRepository::class)
 */
class PrmAffectationProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"affectation"})
     */
    private $id;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="projet",cascade={"persist"})
     * @JMS\Groups({"affectation"})
     */
    private $user;

    /**
     * @var PrmProjet
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProjet")
     * @JMS\Groups({"affectation"})
     */
    private $projet;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"affectation"})
     */
    private $date_validation;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Groups({"affectation"})
     */
    private $date_affectation;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     * @JMS\Groups({"affectation"})
     */
    private $valide;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 0})
     * @JMS\Groups({"affectation"})
     */
    private $profilValide;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProfil",inversedBy="affectation",cascade={"persist"})
     */
    private $profil;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $niveau;

    /**
     * @ORM\ManyToOne(targetEntity=PrmAdministration::class)
     *
     */
    private $administration;

    /**
     * @ORM\ManyToOne(targetEntity=PrmZoneGeo::class)
     *
     */
    private $region;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isInstitutionCollect = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getProjet()
    {
        return $this->projet;
    }

    public function setProjet($projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateValidation()
    {
        return $this->date_validation;
    }

    /**
     * @param mixed $date_validation
     */
    public function setDateValidation($date_validation)
    {
        $this->date_validation = $date_validation;
    }

    public function getDateAffectation(): ?\DateTimeInterface
    {
        return $this->date_affectation;
    }

    public function setDateAffectation(\DateTimeInterface $date_affectation): self
    {
        $this->date_affectation = $date_affectation;

        return $this;
    }

    public function getValide(): ?string
    {
        return $this->valide;
    }

    public function setValide(string $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProfil()
    {
        return $this->profil;
    }

    /**
     * @param mixed $profil
     */
    public function setProfil($profil)
    {
        $this->profil = $profil;
    }

    /**
     * @return int
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * @param int $niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }

    /**
     * @return mixed
     */
    public function getProfilValide()
    {
        return $this->profilValide;
    }

    /**
     * @param mixed $profilValide
     */
    public function setProfilValide($profilValide)
    {
        $this->profilValide = $profilValide;
    }

    /**
     * @return mixed
     */
    public function getAdministration()
    {
        return $this->administration;
    }

    /**
     * @param mixed $administration
     */
    public function setAdministration($administration)
    {
        $this->administration = $administration;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param mixed $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getIsInstitutionCollect()
    {
        return $this->isInstitutionCollect;
    }

    /**
     * @param mixed $isInstitutionCollect
     */
    public function setIsInstitutionCollect($isInstitutionCollect): void
    {
        $this->isInstitutionCollect = $isInstitutionCollect;
    }


}
