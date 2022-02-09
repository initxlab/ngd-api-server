<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Initxlab\Ngd\Params\C;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(
    collectionOperations: [C::_GET,C::_POST],
    itemOperations: [
    C::_GET=>[
        C::NORMALIZATION_CONTEXT=>[
            C::GROUPS=>[
                C::R_USER,
                C::ITEM_GET_USER
            ]
        ]
    ],
    C::_PUT
],
    attributes: [
    C::PAGINATION_PER_PAGE => 5,
    C::FORMATS=>[
        C::F_JSONLD,
        C::F_JSON,
        C::F_HTML,
        C::F_JSONHAL,
        C::F_CSV=>[C::MIME_TXT_CSV]
    ]
],
    denormalizationContext: [C::GROUPS=>[C::W_USER]],
    normalizationContext: [C::GROUPS=>[C::R_USER]]
)]
// ADDING PROPERTY FILTERS - See README.MD for usage
#[ApiFilter(PropertyFilter::class)]
#[UniqueEntity(fields: [C::PROP_USERNAME])]
#[UniqueEntity(fields: [C::PROP_EMAIL])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private const ROLE_USER = "ROLE_USER";
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[Groups([C::R_USER,C::W_USER])]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private string $email;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    #[Groups([C::R_USER,C::W_USER])]
    #[Assert\NotBlank()]
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    #[Groups([C::R_USER,C::W_USER,C::ITEM_GET_WAREHOUSE,C::W_WAREHOUSE])]
    #[Assert\NotBlank()]
    private string $username;

    /**
     * @ORM\OneToMany(targetEntity=NgdWarehouse::class, mappedBy="owner",cascade={"persist"},orphanRemoval=true)
     * @Assert\Valid()
     */
    #[Groups([C::ITEM_GET_USER,C::W_USER])]
    private $warehouses;

    public function __construct()
    {
        $this->warehouses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials():void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getWarehouses(): Collection
    {
        return $this->warehouses;
    }

    public function addWarehouse(NgdWarehouse $warehouse): self
    {
        if (!$this->warehouses->contains($warehouse)) {
            $this->warehouses[] = $warehouse;
            $warehouse->setOwner($this);
        }

        return $this;
    }

    public function removeWarehouse(NgdWarehouse $warehouse): self
    {
        // set the owning side to null (unless already changed)
        if ($this->warehouses->removeElement($warehouse) && $warehouse->getOwner() === $this) {
            $warehouse->setOwner(null);
        }

        return $this;
    }

    #[Groups([C::R_USER])]
    #[SerializedName(C::PRO_STAT_STOCK)]
    public function getCountProducts() : int
    {
        return $this->warehouses->count();
    }
}
