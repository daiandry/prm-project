<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PrmTachesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use App\Annotation\TrackableClass;

/**
 * @TrackableClass()
 * @ApiResource(
 *     normalizationContext={"groups"={"tache:read"}},
 *     denormalizationContext={"groups"={"tache:write"}}
 * )
 * @ORM\Entity(repositoryClass=PrmTachesRepository::class)
 */
class PrmTaches
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"projet-taches","tache:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"projet-taches","tache:write","tache:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"tache:write","tache:read"})
     */
    private $dateRealisationPrevu;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"tache:write","tache:read"})
     */
    private $dateRealisationReel;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:write","tache:read"})
     */
    private $avancement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:write","tache:read"})
     */
    private $observation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:write","tache:read"})
     */
    private $valeurPrevu;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:write","tache:read"})
     */
    private $valeurReel;


    /**
     * @var PrmProjet|null
     * @ORM\ManyToOne(targetEntity=PrmProjet::class, inversedBy="taches")
     * @SerializedName("projet")
     * @Groups({"tache:write","tache:read"})
     */
    private $projet;

    /**
     * @var PrmStatutTache|null
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmStatutTache",inversedBy="tache",cascade={"persist"})
     * @Groups({"tache:write","tache:read"})
     */
    private $statut;


    /**
     * @var PrmTypeTache|null
     * @ORM\ManyToOne(targetEntity=PrmTypeTache::class)
     * @Groups({"tache:write","tache:read"})
     */
    private $typeTache;

    /**
     * @var PrmCategorieTache|null
     * @ORM\ManyToOne(targetEntity=PrmCategorieTache::class)
     * @Groups({"tache:write","tache:read"})
     */
    private $categorie;

    /**
     * @var PrmPhotos|null
     * @ORM\OneToMany(targetEntity="App\Entity\PrmPhotos",mappedBy="tache",cascade={"persist"})
     * @Groups({"tache:write","tache:read","projet-taches"})
     */
    private $photos;
    /**
     * @var PrmDocuments|null
     * @ORM\OneToMany(targetEntity="App\Entity\PrmDocuments",mappedBy="tache",cascade={"persist"})
     * @Groups({"tache:write","tache:read","projet-taches"})
     */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity=PrmUniteIndicateur::class)
     * @Groups({"tache:write","tache:read"})
     */
    private $uniteIndicateur;

    /**
     * @ORM\ManyToOne(targetEntity=PrmUniteMonetaire::class)
     * @Groups({"tache:write","tache:read"})
     */
    private $uniteMonetaire;


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

    public function getProjetId(): ?PrmProjet
    {
        return $this->projet;
    }

    public function setProjetId(?PrmProjet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getDateRealisationPrevu(): ?\DateTimeInterface
    {
        return $this->dateRealisationPrevu;
    }

    public function setDateRealisationPrevu(\DateTimeInterface $dateRealisationPrevu): self
    {
        $this->dateRealisationPrevu = $dateRealisationPrevu;

        return $this;
    }

    public function getDateRealisationReel(): ?\DateTimeInterface
    {
        return $this->dateRealisationReel;
    }

    public function setDateRealisationReel(\DateTimeInterface $dateRealisationReel): self
    {
        $this->dateRealisationReel = $dateRealisationReel;

        return $this;
    }

    public function getAvancement(): ?string
    {
        return $this->avancement;
    }

    public function setAvancement(string $avancement): self
    {
        $this->avancement = $avancement;

        return $this;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getValeurPrevu(): ?string
    {
        return $this->valeurPrevu;
    }

    public function setValeurPrevu(string $valeurPrevu): self
    {
        $this->valeurPrevu = $valeurPrevu;

        return $this;
    }

    public function getValeurReel(): ?string
    {
        return $this->valeurReel;
    }

    public function setValeurReel(string $valeurReel): self
    {
        $this->valeurReel = $valeurReel;

        return $this;
    }

    public function getProjet(): ?PrmProjet
    {
        return $this->projet;
    }

    public function setProjet(?PrmProjet $projet): self
    {
        $this->projet = $projet;

        return $this;
    }

    public function getCategorie(): ?PrmCategorieTache
    {
        return $this->categorie;
    }

    public function setCategorie(?PrmCategorieTache $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getTypeTache(): ?PrmTypeTache
    {
        return $this->typeTache;
    }

    public function setTypeTache(?PrmTypeTache $typeTache): self
    {
        $this->typeTache = $typeTache;

        return $this;
    }

    /**
     * @return
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param
     */
    public function setPhotos($photos): void
    {
        foreach ($photos as $photo) {
            $photo->setTache($this);
        }
        $this->photos = $photos;
    }

    /**
     * @return
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param
     */
    public function setDocument($documents)
    {
        foreach ($documents as $document) {
            $document->setTache($this);
        }
        $this->document = $documents;
    }

    public function getUniteIndicateur(): ?PrmUniteIndicateur
    {
        return $this->uniteIndicateur;
    }

    public function setUniteIndicateur(?PrmUniteIndicateur $uniteIndicateur): self
    {
        $this->uniteIndicateur = $uniteIndicateur;

        return $this;
    }

    public function getUniteMonetaire(): ?PrmUniteMonetaire
    {
        return $this->uniteMonetaire;
    }

    public function setUniteMonetaire(?PrmUniteMonetaire $uniteMonetaire): self
    {
        $this->uniteMonetaire = $uniteMonetaire;

        return $this;
    }

}
