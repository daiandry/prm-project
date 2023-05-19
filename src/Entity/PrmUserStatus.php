<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PrmUserStatusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *     itemOperations={"get"},
 *     collectionOperations={}
 * )
 * @ORM\Entity(repositoryClass=PrmUserStatusRepository::class)
 */
class PrmUserStatus
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:write","user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"user:write","user:read"})
     *
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"user:write","user:read"})
     *
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Groups({"user:read"})
     */
    private $couleur;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * @param mixed $couleur
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;
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
}
