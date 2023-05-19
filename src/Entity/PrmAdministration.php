<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PrmAdministrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     attributes={"pagination_enabled"=false},
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"get"}, "enable_max_depth"=true}
 * )
 *
 * @ORM\Entity(repositoryClass=PrmAdministrationRepository::class)
 */
class PrmAdministration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:write","user:read","get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:write","user:read","get"})
     *
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:write","user:read","get"})
     *
     */
    private $code;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $leftBound;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rightBound;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmTypeAdmin",inversedBy="admin",cascade={"persist"})
     * @Groups({"user:read"})
     * @MaxDepth(1)
     */
    private $typeAdmin;

    /**
     * @ORM\ManyToOne(targetEntity=PrmAdministration::class, inversedBy="prmAdministrations")
     * @MaxDepth(1)
     */
    private $parentId;

    /**
     * @ORM\OneToMany(targetEntity=PrmAdministration::class, mappedBy="parentId")
     * @MaxDepth(1)
     */
    private $prmAdministrations;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $parentLibelle;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $niveau;

    public function __construct()
    {
        $this->prmAdministrations = new ArrayCollection();
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

    public function getLeftBound(): ?int
    {
        return $this->leftBound;
    }

    public function setLeftBound(?int $leftBound): self
    {
        $this->leftBound = $leftBound;

        return $this;
    }

    public function getRightBound(): ?int
    {
        return $this->rightBound;
    }

    public function setRightBound(?int $rightBound): self
    {
        $this->rightBound = $rightBound;

        return $this;
    }

    public function getTypeAdmin()
    {
        return $this->typeAdmin;
    }

    public function setTypeAdmin($typeAdmin)
    {
        $this->typeAdmin = $typeAdmin;

        return $this;
    }

    public function getParentId(): ?self
    {
        return $this->parentId;
    }

    public function setParentId(?self $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getPrmAdministrations(): Collection
    {
        return $this->prmAdministrations;
    }

    public function addPrmAdministration(self $prmAdministration): self
    {
        if (!$this->prmAdministrations->contains($prmAdministration)) {
            $this->prmAdministrations[] = $prmAdministration;
            $prmAdministration->setParentId($this);
        }

        return $this;
    }

    public function removePrmAdministration(self $prmAdministration): self
    {
        if ($this->prmAdministrations->contains($prmAdministration)) {
            $this->prmAdministrations->removeElement($prmAdministration);
            // set the owning side to null (unless already changed)
            if ($prmAdministration->getParentId() === $this) {
                $prmAdministration->setParentId(null);
            }
        }

        return $this;
    }

    public function getParentLibelle(): ?string
    {
        return $this->parentLibelle;
    }

    public function setParentLibelle(?string $parentLibelle): self
    {
        $this->parentLibelle = $parentLibelle;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * @param mixed $niveau
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;
    }
}
