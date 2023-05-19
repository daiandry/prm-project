<?php

namespace App\Entity;

use App\Repository\PrmSituationProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmSituationProjetRepository::class)
 */
class PrmSituationProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"situation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @JMS\Groups({"situation"})
     */
    private $libelle;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="situation_actuelle_marche",cascade={"persist"})
     */
    private $projet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }
}
