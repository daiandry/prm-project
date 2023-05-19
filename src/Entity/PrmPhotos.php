<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PrmPhotosRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     collectionOperations={"post"},
 *     itemOperations={"get","put"}
 *
 * )
 * @ORM\Entity(repositoryClass=PrmPhotosRepository::class)
 */
class PrmPhotos
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:write","user:read","tache:write","tache:read","projet-taches"})
     * @Assert\NotBlank(message="Nom de photo vide")
     *
     */
    private $nom;

    /**
     * @ORM\Column(type="text", length=255,nullable=true)
     * @Groups({"tache:write","tache:read","tache:write","tache:read", "projet-taches"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Groups({"user:write","user:read","tache:write","tache:read", "projet-taches"})
     * @Assert\NotBlank(message="Objet image vide")
     *
     */
    private $chemin;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $upload_date;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    private $statut;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmTaches",inversedBy="typeProjet")
     */
    private $tache;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProjet",inversedBy="photos",cascade={"persist"})
     */
    private $projet;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:write","user:read","tache:write","tache:read"})
     */
    private $mimetype;

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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getChemin()
    {
        return $this->chemin;
    }

    /**
     * @param mixed $chemin
     */
    public function setChemin($chemin)
    {
        $this->chemin = $chemin;
    }

    /**
     * @return mixed
     */
    public function getUploadDate()
    {
        return $this->upload_date;
    }

    /**
     * @param mixed $upload_date
     */
    public function setUploadDate($upload_date)
    {
        $this->upload_date = $upload_date;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return int
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * @param int $tache
     */
    public function setTache($tache)
    {
        $this->tache = $tache;
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

    public function getMimetype(): ?string
    {
        return $this->mimetype;
    }

    public function setMimetype(?string $mimetype): self
    {
        $this->mimetype = $mimetype;

        return $this;
    }
}
