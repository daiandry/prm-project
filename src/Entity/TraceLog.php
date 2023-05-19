<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation as JMS;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"log:read"}},
 *     denormalizationContext={"groups"={"log:write"}},
 *     itemOperations={"get"},
 *     collectionOperations={"get"}
 *
 * )
 * @ORM\Entity(repositoryClass="App\Repository\TraceLogRepository")
 */
class TraceLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"log:read"})
     * @JMS\Groups({"log:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"log:read","log:write"})
     * @JMS\Groups({"log:read"})
     */
    private $classeName;

    /**
     * @ORM\Column(type="array")
     * @Groups({"log:read","log:write"})
     * @JMS\Groups({"log:read"})
     */
    private $metadata;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist", "remove", "merge"})
     * @Groups({"log:read","log:write"})
     * @JMS\Groups({"log:read"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"log:read","log:write"})
     * @JMS\Groups({"log:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=RessourceType::class)
     * @Groups({"log:read","log:write"})
     * @JMS\Groups({"log:read"})
     */
    private $ressourceType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"log:read","log:write"})
     * @JMS\Groups({"log:read"})
     */
    private $ressourceId;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getClasseName()
    {
        return $this->classeName;
    }

    /**
     * @param $classeName
     *
     * @return History
     */
    public function setClasseName($classeName): self
    {
        $this->classeName = $classeName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param $metadata
     * @return TraceLog
     */
    public function setMetadata($metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }


    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return TraceLog
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return TraceLog
     */
    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRessourceType(): ?RessourceType
    {
        return $this->ressourceType;
    }

    public function setRessourceType(?RessourceType $ressourceType): self
    {
        $this->ressourceType = $ressourceType;

        return $this;
    }

    public function getRessourceId(): ?int
    {
        return $this->ressourceId;
    }

    public function setRessourceId(?int $ressourceId): self
    {
        $this->ressourceId = $ressourceId;

        return $this;
    }
}
