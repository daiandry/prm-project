<?php

namespace App\Entity;

use App\Repository\PrmTypeProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmTypeProjetRepository::class)
 */
class PrmTypeProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"type_projet"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @JMS\Groups({"type_projet"})
     */
    private $libelle;

    /**
     * @var PrmProjet
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="type",cascade={"persist"})
     */
    private $typeProjet;

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

    /**
     * @return PrmProjet
     */
    public function getTypeProjet()
    {
        return $this->typeProjet;
    }

    /**
     * @param PrmProjet $typeProjet
     */
    public function setTypeProjet($typeProjet)
    {
        $this->typeProjet = $typeProjet;
    }
}
