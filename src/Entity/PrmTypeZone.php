<?php

namespace App\Entity;

use App\Repository\PrmTypeZoneRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PrmTypeZoneRepository::class)
 */
class PrmTypeZone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"list_type_zone"})
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @JMS\Groups({"list_type_zone"})
     * @Groups({"user:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=100)
     * @JMS\Groups({"list_type_zone"})
     * @Groups({"user:read"})
     */
    private $code;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmZoneGeo",mappedBy="type",cascade={"persist"})
     */
    private $PrmZoneGeo;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
