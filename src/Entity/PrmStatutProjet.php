<?php

namespace App\Entity;

use App\Repository\PrmStatutProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmStatutProjetRepository::class)
 */
class PrmStatutProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"statut"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @JMS\Groups({"statut"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @JMS\Groups({"statut"})
     */
    private $couleur;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @JMS\Groups({"statut"})
     */
    private $progressbar;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="statut",cascade={"persist"})
     */
    private $projet;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmHistoriqueAvancement",mappedBy="statut",cascade={"persist"})
     */
    private $historique_avancement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
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

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * @param mixed $couleur
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;
    }

    /**
     * @return mixed
     */
    public function getProgressbar()
    {
        return $this->progressbar;
    }

    /**
     * @param mixed $progressbar
     */
    public function setProgressbar($progressbar)
    {
        $this->progressbar = $progressbar;
    }
}
