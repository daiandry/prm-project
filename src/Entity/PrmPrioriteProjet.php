<?php

namespace App\Entity;

use App\Repository\PrmPrioriteProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmPrioriteProjetRepository::class)
 * @ORM\EntityListeners({"App\EventListener\PrioriteProjetListener"})
 */
class PrmPrioriteProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"priorite"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @JMS\Groups({"priorite"})
     */
    private $libelle;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="priorite",cascade={"persist"})
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
