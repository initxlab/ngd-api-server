<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\NgdProductLineRepository;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Initxlab\Ngd\Base\ApiEntityBase;
use Initxlab\Ngd\Params\C;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

// FILTERS
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

/**
 * @ORM\Entity(repositoryClass=NgdProductLineRepository::class)
 */
#[ApiResource (
    collectionOperations: [C::_GET,C::_POST],
    itemOperations: [
    C::_GET=>[
        C::NORMALIZATION_CONTEXT => [
            C::GROUPS=>[
                C::R_PRODUCT_LINE,
                C::ITEM_GET_PRODUCT_LINE
            ]
        ]
    ],
    C::_PUT,
    C::_DELETE
],
    shortName: C::SHORT_NAME_PRODUCT_LINE,
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
    denormalizationContext: [
    C::GROUPS=>[
        C::W_PRODUCT_LINE
    ],
    C::SWAGGER_DEFINITION_NAME=>C::SWAGGER_W_LABEL_PRODUCT_LINE
],
    normalizationContext: [
        C::GROUPS=>[
            C::R_PRODUCT_LINE
        ],
        C::SWAGGER_DEFINITION_NAME => C::SWAGGER_R_LABEL_PRODUCT_LINE,
    ]
)]
// CLIENT REQUEST FILTERS
#[ApiFilter(BooleanFilter::class,properties: [C::PROP_IS_PUBLISHED])]
#[ApiFilter(SearchFilter::class,properties: [
    C::PROP_NAME=>C::MATCH_PARTIAL,
    C::PROP_DESC=>C::MATCH_PARTIAL,
])]
#[UniqueEntity(fields: [C::PROP_NAME])]
class NgdProductLine extends ApiEntityBase
{
    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    #[Groups([C::R_PRODUCT_LINE,C::W_PRODUCT_LINE])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    /**
     * @ORM\Column(type="text",nullable=false)
     */
    #[Groups([C::W_PRODUCT_LINE])]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 1024)]
    private string $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Groups([C::R_PRODUCT_LINE,C::W_PRODUCT_LINE])]
    #[SerializedName(C::LABEL_ACTIVE)]
    private ?bool $isPublished = false;

    /**
     * @ORM\OneToMany(targetEntity=NgdWarehouse::class, mappedBy="product", orphanRemoval=true)
     */
    #[Groups([C::ITEM_GET_PRODUCT_LINE])]
    #[SerializedName(C::LABEL_STOCKS)]
    private $warehouses;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        parent::__construct();
        $this->warehouses = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Not exposed method as not bind to a group
     * @param string $description
     * @return NgdProductLine
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Short description of the product
     * @param string $description
     * @return NgdProductLine
     */
    #[Groups([C::R_PRODUCT_LINE])]
    public function setTextDescription(string $description): self
    {
        $this->description = nl2br($description);

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     * @return $this
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    /**
     * @param bool $isPublished
     * @return $this
     */
    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return string|null
     */
    #[Groups([C::R_PRODUCT_LINE])]
    #[SerializedName(C::LABEL_DESCRIPTION)]
    public function getShortDescription(): ?string {
        if(strlen($this->description) < 40){
            return $this->description;
        }
        return substr($this->description,0,40).'...';
    }

    /**
     * How long ago the record was created
     * @return string
     */
    #[Groups([C::R_PRODUCT_LINE])]
    #[SerializedName(C::LABEL_CREATED_AT)]
    public function getCreatedAtAgo(): string {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
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
            $warehouse->setProduct($this);
        }

        return $this;
    }

    public function removeWarehouseStock(NgdWarehouse $warehouse): self
    {
        // set the owning side to null (unless already changed)
        if ($this->warehouses->removeElement($warehouse) && $warehouse->getProduct() === $this) {
            $warehouse->setProduct(null);
        }
        return $this;
    }

    #[Groups([C::R_PRODUCT_LINE])]
    #[SerializedName(C::LABEL_STAT_PRODUCT)]
    public function getCountProducts() : int
    {
        return $this->warehouses->count();
    }
}
