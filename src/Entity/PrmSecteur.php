<?php

namespace App\Entity;

use App\Repository\PrmSecteurRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmSecteurRepository::class)
 * @ORM\EntityListeners({"App\EventListener\SecteurListener"})
 */
class PrmSecteur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"secteur"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"secteur","mail:inaugurable"})
     */
    private $libelle;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="secteur",cascade={"persist"})
     */
    private $PrmProjet;

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

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function setTypeId(int $type_id): self
    {
        $this->type_id = $type_id;

        return $this;
    }
}
