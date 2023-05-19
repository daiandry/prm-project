<?php

namespace App\Entity;

use App\Repository\PrmDocumentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PrmDocumentsRepository::class)
 */
class PrmDocuments
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:write","tache:read"})
     */
    private $nom;

    /**
     * @var PrmProjet
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProjet",inversedBy="doc",cascade={"persist"})
     */
    private $projet;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:write","tache:read"})
     */
    private $chemin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $upload_date;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     * @Groups({"tache:write","tache:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $statut;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmTaches",inversedBy="typeProjet",cascade={"persist"})
     */
    private $tache;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmDocType",inversedBy="document",cascade={"persist"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"tache:write","tache:read"})
     */
    private $mimetype;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default" : 1})
     */
    private $enabled;

    public function __construct()
    {
        $this->upload_date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return PrmProjet
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * @param PrmProjet $projet
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param mixed $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }
}
