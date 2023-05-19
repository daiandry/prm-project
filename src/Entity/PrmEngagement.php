<?php

namespace App\Entity;

use App\Repository\PrmEngagementRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmEngagementRepository::class)
 * @ORM\EntityListeners({"App\EventListener\EngagementListener"})
 */
class PrmEngagement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"engagement"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"engagement","mail:inaugurable"})
     */
    private $libelle;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="engagement",cascade={"persist"})
     */
    private $PrmProjet;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return int
     */
    public function getPrmProjet()
    {
        return $this->PrmProjet;
    }

    /**
     * @param int $PrmProjet
     */
    public function setPrmProjet($PrmProjet)
    {
        $this->PrmProjet = $PrmProjet;
    }
}
