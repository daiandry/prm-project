<?php

namespace App\Entity;

use App\Repository\PrmZoneGeoRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     itemOperations={
 *          "get"={
 *              "method": "GET",
 *              "controller": SomeRandomController::class}
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method": "GET",
 *              "controller": SomeRandomController::class
 *          }
 *     }
 * )
 * @ORM\Entity(repositoryClass=PrmZoneGeoRepository::class)
 */
class PrmZoneGeo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"list_zone"})
     */
    private $id;

    /**
     * @var
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmTypeZone",inversedBy="PrmZoneGeo",cascade={"persist"})
     * @Groups({"user:read"})
     * @MaxDepth(1)
     *
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=100)
     * @JMS\Groups({"list_zone","mail:inaugurable"})
     * @Groups({"user:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=100)
     * @JMS\Groups({"list_zone"})
     * @Groups({"user:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="geometry",nullable=true)
     */
    private $geom;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $left_bound;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $right_bound;

    /**
     * @var PrmZoneGeo
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmZoneGeo")
     */
    private $parent;

    /**
     * @var \Doctrine\Common\Collections\Collection|PrmProjet[]
     *
     * @ORM\ManyToMany(targetEntity="\App\Entity\PrmProjet", mappedBy="zone",cascade={"persist"})
     */
    protected $projet;

    /**
     * @ORM\Column(type="text",nullable=true)
     * @JMS\Groups({"list_zone","mail:inaugurable"})
     * @Groups({"user:read"})
     */
    private $geoRef;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     *
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getgeoRef()
    {
        return $this->geoRef;
    }

    /**
     * @param mixed $geoRef
     */
    public function setgeoRef($geoRef)
    {
        $this->geoRef = $geoRef;
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

    public function getLeftBound()
    {
        return $this->left_bound;
    }

    public function setLeftBound($left_bound)
    {
        $this->left_bound = $left_bound;

        return $this;
    }

    public function getRightBound()
    {
        return $this->right_bound;
    }

    public function setRightBound($right_bound)
    {
        $this->right_bound = $right_bound;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGeom()
    {
        return $this->geom;
    }

    /**
     * @param mixed $geom
     */
    public function setGeom($geom)
    {
        $this->geom = $geom;
    }

    /**
     * @return PrmZoneGeo
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param PrmZoneGeo $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return PrmProjet[]|\Doctrine\Common\Collections\Collection
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * @param PrmProjet[]|\Doctrine\Common\Collections\Collection $projet
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;
    }
}
