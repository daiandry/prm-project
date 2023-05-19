<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PrmProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as JMS;

/**
 * @ApiResource(
 *     
 *     itemOperations={
 *          "put","patch","delete",
 *          "get"={
 *              "normalization_context"={"groups"={"profil:get"}}
 *          }
 *     },
 *     paginationEnabled=false,
 *     paginationClientItemsPerPage=false
 *
 * )
 * @ORM\Entity(repositoryClass=PrmProfilRepository::class)
 */
class PrmProfil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"affectation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     *
     * @Groups({"profil:get","user:write","user:read","log:read","log:write"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"profil","user:write","user:read","log:read","log:write"})
     *
     *
     */
    private $code;

    /**
     * @ORM\ManyToMany(targetEntity=PrmDroit::class, inversedBy="prmProfils")
     * @Groups({"user:read"})
     *
     */
    private $droits;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"profil","user:read"})
     */
    private $status;

    /**
     * @var PrmAffectationProjet
     * @ORM\OneToMany(targetEntity="App\Entity\PrmAffectationProjet",mappedBy="profil",cascade={"persist"})
     * @JMS\Groups({"affectation"})
     */
    private $affectation;

    /**
     * @var \Doctrine\Common\Collections\Collection|PrmProjet[]
     *
     * @ORM\ManyToMany(targetEntity="\App\Entity\PrmProjet", mappedBy="profil",cascade={"persist"})
     */
    protected $projet;

    /**
     * @var
     * @ORM\OneToMany(targetEntity="\App\Entity\User", mappedBy="profil")
     */
    private $users;

    public function __construct()
    {
        $this->droits = new ArrayCollection();
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
     * @return Collection|PrmDroit[]
     */
    public function getDroits(): Collection
    {
        return $this->droits;
    }

    public function addDroit(PrmDroit $droit): self
    {
        if (!$this->droits->contains($droit)) {
            $this->droits[] = $droit;
        }

        return $this;
    }

    public function removeDroit(PrmDroit $droit): self
    {
        if ($this->droits->contains($droit)) {
            $this->droits->removeElement($droit);
        }

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return PrmProjet[]|Collection
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * @param PrmProjet[]|Collection $projet
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;
    }

    /**
     * @return User[]|Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

}
