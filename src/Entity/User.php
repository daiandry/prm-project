<?php
// src/Entity/User.php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Annotation\TrackableClass;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\GroupInterface;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ResetPassword;
use App\Controller\ChangePassword;
use App\Controller\SendMailUser;
use App\Controller\ResettingCheckToken;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\ListUsers;
use App\Controller\ChangeOldPassword;
use App\Controller\DisableUser;
use JMS\Serializer\Annotation as JMS;
use App\Controller\PhotoProfilUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @TrackableClass()
 * @UniqueEntity(fields={"email"}, message="Email dupliqué")
 * @ApiFilter(SearchFilter::class, properties={"nom"="partial", "email"="partial", "enabled"="exact", "administration"="exact", "region"="exact"})
 * @ApiResource(
 *     attributes={"pagination_client_items_per_page"=true},
 *     normalizationContext={"groups"={"user:read"}},
 *     denormalizationContext={"groups"={"user:write"}},
 *     collectionOperations={
 *          "post",
 *          "get_list_users"={
 *              "name"="api_user_list",
 *              "method"="GET",
 *              "path"="/user/list",
 *              "controller"=ListUsers::class,
 *              "normalization_context"={"groups"={"user:read"}},
 *
 *              "swagger_context"={
 *                  "summary" = "List User",
 *                  "parameters"={
 *                      {
 *                          "name" = "page",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      },{
 *                          "name" = "itemsPerPage",
 *                          "in" = "path",
 *                          "type"="integer",
 *                          "required" = "true"
 *                      },{
 *                          "name" = "email",
 *                          "in" = "path",
 *                          "type"="string",
 *                      },{
 *                          "name" = "nom",
 *                          "in" = "path",
 *                          "type"="string",
 *                          "required" = "true"
 *                      },{
 *                          "name" = "enabled",
 *                          "in" = "path",
 *                          "type"="boolean",
 *                      },{
 *                          "name" = "ministere",
 *                          "in" = "path",
 *                          "type"="integer",
 *                      }
 *
 *                  },
 *              }
 *          },
 *          "post_resetting_sendmail"={
 *              "name"="api_user_resetting_send_mail",
 *              "method"="POST",
 *              "path"="/user/resetting/send-mail",
 *              "controller"=SendMailUser::class,
 *              "normalization_context"={"groups"={"send-mail"}},
 *              "denormalization_context"={"groups"={"send-mail"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Send mail for reset password",
 *                  "parameters"={
 *                      {
 *                          "name" = "User",
 *                          "in" = "body",
 *                          "schema" = {
 *                              "type" = "object",
 *                              "properties" = {
 *                                  "email" = {"type"="string", "required"="true"}
 *                              }
 *                           },
 *                          "required" = "true",
 *                      }
 *                  },
 *              }
 *          },
 *          "post_change_password"={
 *              "name"="api_user_change_password",
 *              "method"="POST",
 *              "path"="/change-password",
 *              "controller"=ChangePassword::class,
 *              "normalization_context"={"groups"={"change-password"}},
 *              "denormalization_context"={"groups"={"change-password"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Change user password",
 *                  "parameters"={
 *                      {
 *                          "name" = "User",
 *                          "in" = "body",
 *                          "schema" = {
 *                              "type" = "object",
 *                              "properties" = {
 *                                  "password" = {"type"="string", "required"="true"},
 *                                  "token" = {"type"="string", "required"="true"}
 *                              }
 *                           },
 *                          "required" = "true",
 *                      }
 *                  },
 *              }
 *          },
 *          "post_resetting_password"={
 *              "name"="api_user_resetting_password",
 *              "method"="POST",
 *              "path"="/user/resetting-password/{token}",
 *              "controller"=ResetPassword::class,
 *              "normalization_context"={"groups"={"reset-password"}},
 *              "denormalization_context"={"groups"={"reset-password"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Change user password",
 *                  "parameters"={
 *                      {
 *                          "name" = "User",
 *                          "in" = "body",
 *                          "schema" = {
 *                              "type" = "object",
 *                              "properties" = {
 *                                  "password" = {"type"="string", "required"="true"}
 *                              }
 *                           },
 *                          "required" = "true",
 *                      }
 *                  },
 *              }
 *          },
 *          "post_change_old_password"={
 *              "name"="api_user_change_old_password",
 *              "method"="POST",
 *              "path"="/user/change-old-password",
 *              "controller"=ChangeOldPassword::class,
 *              "normalization_context"={"groups"={"change-old-password"}},
 *              "denormalization_context"={"groups"={"change-old-password"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Change user old password",
 *                  "parameters"={
 *                      {
 *                          "name" = "User",
 *                          "in" = "body",
 *                          "schema" = {
 *                              "type" = "object",
 *                              "properties" = {
 *                                  "password"={"type"="string", "required"="true"},
 *                                   "old_password"={"type"="string", "required"="true"}
 *
 *                              }
 *                           },
 *                          "required" = "true",
 *                      }
 *                  },
 *              }
 *          },
 *          "post_user_disable"={
 *              "name"="api_user_disable",
 *              "method"="POST",
 *              "path"="/user/disable",
 *              "controller"=DisableUser::class,
 *              "normalization_context"={"groups"={"user:disabled"}},
 *              "denormalization_context"={"groups"={"user:disabled"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Disabled user",
 *                  "parameters"={
 *                      {
 *                          "name" = "User",
 *                          "in" = "body",
 *                          "schema" = {
 *                              "type" = "object",
 *                              "properties" = {
 *                                  "status"={"type"="boolean", "required"="true"}
 *
 *                              }
 *                           },
 *                          "required" = "true",
 *                      }
 *                  },
 *              }
 *          }
 *     },
 *     itemOperations={
 *          "get","put","patch",
 *          "get_confirmation_token_resetting"={
 *              "name"="api_resetting_check_confirmation_token",
 *              "method"="GET",
 *              "deserialize"=false,
 *              "path"="/resetting/reset/{token}",
 *              "controller"=ResettingCheckToken::class,
 *              "normalization_context"={"groups"={"check-token"}},
 *              "denormalization_context"={"groups"={"check-token"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Check mail for reset password",
 *                  "parameters"={
 *                      {
 *                          "name" = "token",
 *                          "in" = "path",
 *                          "type"="string",
 *                          "required" = "true"
 *                      }
 *                  }
 *              }
 *          },
 *          "get_photo_profile_user"={
 *              "name"="api_photo_profil_user",
 *              "method"="GET",
 *              "deserialize"=false,
 *              "path"="/user/photo-profil",
 *              "controller"=PhotoProfilUser::class,
 *              "normalization_context"={"groups"={"check-token"}},
 *              "denormalization_context"={"groups"={"check-token"}},
 *              "read"=false,
 *              "swagger_context"={
 *                  "summary" = "Check mail for reset password",
 *                  "parameters"={
 *                      {
 *                          "name" = "token",
 *                          "in" = "path",
 *                          "type"="string",
 *                          "required" = "true"
 *                      }
 *                  }
 *              }
 *          }
 *     }
 *
 * )
 * @ORM\Entity
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="prm_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"user:read"})
     * @JMS\Groups({"log:read","mail:inaugurable","user:read","observation:read"})
     */
    protected $id;

    /**
     * @var string
     *
     */
    protected $username;

    /**
     * @var string
     */
    protected $usernameCanonical;

    /**
     * @var string
     * @Groups({"send-mail","user:read", "user:write"})
     * @JMS\Groups({"log:read","mail:inaugurable","user:read","observation:read"})
     */
    protected $email;

    /**
     * @var string
     */
    protected $emailCanonical;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "user:write"})
     * @JMS\Groups({"log:read","user:read","observation:read"})
     *
     */
    private $nom;

    /**
     * @var bool
     * @Groups({"user:read", "user:write"})
     * @JMS\Groups({"user:read"})
     *
     */
    protected $enabled = true;

    /**
     * The salt to use for hashing.
     *
     * @var string
     */
    protected $salt;

    /**
     * Encrypted password. Must be persisted.
     *
     * @var string
     * @Groups({"change-old-password"})
     * @SerializedName("old_password")
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     * @Groups({"reset-password","change-password", "change-old-password"})
     * @SerializedName("password")
     */
    protected $plainPassword;

    /**
     * @var \DateTime|null
     * @Groups({"user:read"})
     * @JMS\Groups({"user:read"})
     */
    protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it.
     *
     * @var string|null
     * @Groups({"change-password"})
     *
     */
    protected $confirmationToken;

    /**
     * @var \DateTime|null
     */
    protected $passwordRequestedAt;

    /**
     * @var GroupInterface[]|Collection
     */
    protected $groups;

    /**
     * @var array
     * @Groups({"user:read", "user:write"})
     * @JMS\Groups({"user:read"})
     */
    protected $roles;

    /**
     * @ORM\ManyToOne(targetEntity=PrmProfil::class, inversedBy="users")
     * @Groups({"user:read", "user:write"})
     * @JMS\Groups({"user:read"})
     * @ApiSubresource(maxDepth=1)
     *
     */
    private $profil;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $firstLogin = true;

    /**
     * @ORM\ManyToOne(targetEntity=PrmUserStatus::class)
     * @Groups({"user:read", "user:write","user:disabled"})
     * @SerializedName("status")
     * @ApiSubresource(maxDepth=1)
     * @JMS\Groups({"user:read"})
     *
     */
    private $userStatus;

    /**
     * @var int
     * @Groups({"user:disabled"})
     */
    private $listUser;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tn;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"user:read", "user:write"})
     * @JMS\Groups({"user:read"})
     */
    private $locked = false;

    /**
     * @var \DateTime|null
     */
    private $lockedUntil;

    /**
     * @ORM\ManyToOne(targetEntity=PrmAdministration::class)
     * @Groups({"user:read", "user:write"})
     * @SerializedName("ministere")
     * @ApiSubresource(maxDepth=1)
     * @MaxDepth(1)
     * @JMS\Groups({"user:read"})
     *
     */
    private $administration;

    /**
     * @ORM\OneToOne(targetEntity=PrmPhotos::class, cascade={"persist", "remove"})
     * @Groups({"user:read", "user:write"})
     * @ApiSubresource(maxDepth=1)
     * @JMS\Groups({"user:read"})
     */
    private $photo;

    /**
     * @var integer
     * @ORM\OneToMany(targetEntity="App\Entity\PrmProjet",mappedBy="created_by",cascade={"persist"})
     * @ApiSubresource(maxDepth=1)
     * @Groups({"user:read", "user:write"})
     * @JMS\Groups({"user:read"})
     */
    private $projet;

    /**
     * @ORM\ManyToOne(targetEntity=PrmZoneGeo::class)
     * @Groups({"user:read", "user:write"})
     * @MaxDepth(1)
     * @JMS\Groups({"user:read"})
     *
     */
    private $region;

    /**
     * @var PrmPhotos
     * @ORM\OneToMany(targetEntity="App\Entity\PrmHistoriqueAvancement",mappedBy="auteur",cascade={"persist"})
     */
    private $historique_avanvement;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $createdBy;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->enabled = true;
        $this->username = $this->email;
        $this->roles = array();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getUsername();
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (13 === count($data)) {
            // Unserializing a User object from 1.3.x
            unset($data[4], $data[5], $data[6], $data[9], $data[10]);
            $data = array_values($data);
        } elseif (11 === count($data)) {
            // Unserializing a User from a dev version somewhere between 2.0-alpha3 and 2.0-beta1
            unset($data[4], $data[7], $data[8]);
            $data = array_values($data);
        }

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
            ) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (bool)$boolean;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        return $this->groups ?: $this->groups = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupNames()
    {
        $names = array();
        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup($name)
    {
        return in_array($name, $this->getGroupNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addGroup(GroupInterface $group)
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function getProfil(): ?PrmProfil
    {
        return $this->profil;
    }

    public function setProfil(?PrmProfil $profil): self
    {
        $this->profil = $profil;

        return $this;
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

    public function getFirstLogin(): ?bool
    {
        return $this->firstLogin;
    }

    public function setFirstLogin(bool $firstLogin): self
    {
        $this->firstLogin = $firstLogin;

        return $this;
    }

    public function getTn()
    {
        return $this->tn;
    }

    public function setTn($tn)
    {
        $this->tn = $tn;

        return $this;
    }

    /**
     * Get the value of locked
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set the value of locked
     *
     * @return  self
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get the value of lockedUntil
     *
     * @return  \DateTime|null
     */
    public function getLockedUntil()
    {
        return $this->lockedUntil;
    }

    /**
     * Set the value of lockedUntil
     *
     * @param  \DateTime|null $lockedUntil
     *
     * @return  self
     */
    public function setLockedUntil($lockedUntil)
    {
        $this->lockedUntil = $lockedUntil;

        return $this;
    }

    /**
     * Get the value of userStatus
     */
    public function getUserStatus()
    {
        return $this->userStatus;
    }

    /**
     * Set the value of userStatus
     *
     * @return  self
     */
    public function setUserStatus($userStatus)
    {
        $this->userStatus = $userStatus;

        return $this;
    }

    public function getAdministration(): ?PrmAdministration
    {
        return $this->administration;
    }

    public function setAdministration(?PrmAdministration $administration): self
    {
        $this->administration = $administration;

        return $this;
    }

    public function getPhoto(): ?PrmPhotos
    {
        return $this->photo;
    }

    public function setPhoto(?PrmPhotos $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function prePersist()
    {
        if ($this->userStatus) {
            $this->enabled = $this->userStatus->getId() == 1 ? true : false;
        }
        if ($this->profil) {
            $droits = $this->profil->getDroits();
            if ($droits) {
                foreach ($droits as $droit) {
                    $this->addRole($droit->getCode());
                }
            }
        }
    }

    public function getRegion(): ?PrmZoneGeo
    {
        return $this->region;
    }

    public function setRegion(?PrmZoneGeo $region): self
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @Assert\Callback()
     * @param ExecutionContextInterface $context
     * @param $payload
     */
    public function validate(ExecutionContextInterface $context)
    {

            if(!is_null($this->photo)) {

                if (is_null($this->photo->getNom())) {

                    $context->buildViolation("Nom d'image vide")
                            ->atPath('nom')
                            ->addViolation();
                }
                if (is_null($this->photo->getChemin())) {
                    $context->buildViolation("Chemin d'objet vide")
                        ->atPath('chemin')
                        ->addViolation();
                }
            }
    }

    public function getCreatedBy(): ?self
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?self $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
