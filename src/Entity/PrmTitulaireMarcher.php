<?php

namespace App\Entity;

use App\Repository\PrmTitulaireMarcherRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmTitulaireMarcherRepository::class)
 */
class PrmTitulaireMarcher
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"titulaire"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Groups({"titulaire","mail:inaugurable"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=150)
     * @JMS\Groups({"titulaire"})
     */
    private $contact;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="pdm_titulaire_du_marche",cascade={"persist"})
     */
    private $projet;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): self
    {
        $this->contact = $contact;

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
