<?php

namespace App\Entity;

use App\Repository\PrmCategorieProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmCategorieProjetRepository::class)
 * @ORM\EntityListeners({"App\EventListener\CategorieProjetListener"})
 */
class PrmCategorieProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @JMS\Groups({"categorie"})
     */
    private $id;

    /**
     * @ORM\Column(type="string",nullable=true, length=150)
     * @JMS\Groups({"categorie"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=150)
     * @JMS\Groups({"categorie"})
     */
    private $libelle;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="categorie",cascade={"persist"})
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

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
