<?php

namespace App\Entity;

use App\Repository\PrmDocTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmDocTypeRepository::class)
 */
class PrmDocType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"type_doc"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150)
     * @JMS\Groups({"type_doc"})
     */
    private $libelle;

    /**
     * @var PrmDocuments
     * @ORM\OneToMany(targetEntity="App\Entity\PrmDocuments",mappedBy="type",cascade={"persist"})
     */
    private $document;

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

    /**
     * @return PrmDocuments
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param PrmDocuments $document
     */
    public function setDocument($document)
    {
        $this->document = $document;
    }
}
