<?php

namespace App\Entity;

use App\Repository\PrmHistoriqueAvancementRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmHistoriqueAvancementRepository::class)
 */
class PrmHistoriqueAvancement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $avancement_physique;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $avancement_financiere;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $budget_prevu;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmStatutProjet",inversedBy="historique_avancement",cascade={"persist"})
     */
    private $statut;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\User",inversedBy="historique_avancement",cascade={"persist"})
     * @JMS\Groups({"historique:avancement"})
     */
    private $auteur;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProjet",inversedBy="historique_avanvement",cascade={"persist"})
     * @JMS\Groups({"historique:avancement"})
     */
    private $projet;

    public function getId()
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getAvancementPhysique()
    {
        return $this->avancement_physique;
    }

    public function setAvancementPhysique($avancement_physique)
    {
        $this->avancement_physique = $avancement_physique;

        return $this;
    }

    public function getAvancementFinanciere()
    {
        return $this->avancement_financiere;
    }

    public function setAvancementFinanciere($avancement_financiere)
    {
        $this->avancement_financiere = $avancement_financiere;

        return $this;
    }

    public function getBudgetPrevu()
    {
        return $this->budget_prevu;
    }

    public function setBudgetPrevu($budget_prevu)
    {
        $this->budget_prevu = $budget_prevu;

        return $this;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    public function getAuteur()
    {
        return $this->auteur;
    }

    public function setAuteur($auteur)
    {
        $this->auteur = $auteur;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * @param int $projet
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;
    }
}
