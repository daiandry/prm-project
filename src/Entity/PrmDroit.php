<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PrmDroitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     attributes={"order"={"bloc":"ASC"}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
 * @ORM\Entity(repositoryClass=PrmDroitRepository::class)
 */
class PrmDroit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil"})
     */
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity=PrmProfil::class, mappedBy="droits")
     */
    private $prmProfils;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"profil"})
     */
    private $bloc;

    public function __construct()
    {
        $this->prmProfils = new ArrayCollection();
    }

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

    /**
     * @return Collection|PrmProfil[]
     */
    public function getPrmProfils(): Collection
    {
        return $this->prmProfils;
    }

    public function addPrmProfil(PrmProfil $prmProfil): self
    {
        if (!$this->prmProfils->contains($prmProfil)) {
            $this->prmProfils[] = $prmProfil;
            $prmProfil->addDroit($this);
        }

        return $this;
    }

    public function removePrmProfil(PrmProfil $prmProfil): self
    {
        if ($this->prmProfils->contains($prmProfil)) {
            $this->prmProfils->removeElement($prmProfil);
            $prmProfil->removeDroit($this);
        }

        return $this;
    }

    public function getBloc(): ?string
    {
        return $this->bloc;
    }

    public function setBloc(?string $bloc): self
    {
        $this->bloc = $bloc;

        return $this;
    }
}
