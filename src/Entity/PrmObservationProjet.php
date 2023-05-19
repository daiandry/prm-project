<?php

namespace App\Entity;

use App\Repository\PrmObservationProjetRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass=PrmObservationProjetRepository::class)
 */
class PrmObservationProjet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Groups({"observation:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @JMS\Groups({"observation:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PrmProjet", cascade={"persist", "remove"})
     * @JMS\Groups({"observation:read"})
     */
    private $projet;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Groups({"observation:read"})
     */
    private $date_update;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"observation:read"})
     */
    private $old_val;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Groups({"observation:read"})
     */
    private $new_val;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * @param mixed $projet
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;
    }

    /**
     * @return mixed
     */
    public function getDateUpdate()
    {
        return $this->date_update;
    }

    /**
     * @param mixed $date_update
     */
    public function setDateUpdate($date_update)
    {
        $this->date_update = $date_update;
    }

    /**
     * @return mixed
     */
    public function getOldVal()
    {
        return $this->old_val;
    }

    /**
     * @param mixed $old_val
     */
    public function setOldVal($old_val)
    {
        $this->old_val = $old_val;
    }

    /**
     * @return mixed
     */
    public function getNewVal()
    {
        return $this->new_val;
    }

    /**
     * @param mixed $new_val
     */
    public function setNewVal($new_val)
    {
        $this->new_val = $new_val;
    }
}
