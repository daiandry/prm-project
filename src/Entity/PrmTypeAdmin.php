<?php

namespace App\Entity;

use App\Repository\PrmTypeAdminRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
/**
 * @ORM\Entity(repositoryClass=PrmTypeAdminRepository::class)
 */
class PrmTypeAdmin
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"type_admin","get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Groups({"user:read"})
     * @JMS\Groups({"type_admin","user:read","get"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=50)
     * @JMS\Groups({"type_admin","user:read"})
     *
     */
    private $code;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmAdministration",mappedBy="typeAdmin",cascade={"persist"})
     */
    private $admin;

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
     * @return PrmAdministration
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param PrmAdministration $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }
}
