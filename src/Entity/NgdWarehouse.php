<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Repository\NgdWarehouseRepository;
use Carbon\Carbon;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Initxlab\Ngd\Base\ApiEntityBase;
use Initxlab\Ngd\Params\C;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * TODO: REMOVE ALL HARD-CODING CALL CONST FROM PARAMS
 * MIGRATE TO NOW NATIVE ANNOTATION SYNTAX
 * @ORM\Entity(repositoryClass=NgdWarehouseRepository::class)
 */
#[ApiResource(
    collectionOperations: [C::_GET,C::_POST],
    itemOperations: [
        C::_GET=>[
            C::NORMALIZATION_CONTEXT=>[
                C::GROUPS=>[
                    C::R_WAREHOUSE,
                    C::ITEM_GET_WAREHOUSE
                ]
            ]
        ],
        C::_PUT
    ],
    shortName: C::SHORT_NAME_WAREHOUSE,
    attributes: [
    C::PAGINATION_PER_PAGE => 5,
    C::FORMATS=>[
        C::F_JSONLD,
        C::F_JSON,
        C::F_HTML,
        C::F_JSONHAL,
        /* Local declaration of a csv format, not global scope from in the api_platform conf. Only apply to this resource */
        C::F_CSV=>[C::MIME_TXT_CSV]
    ]
],
    denormalizationContext: [C::GROUPS=>[C::W_WAREHOUSE],C::SWAGGER_DEFINITION_NAME=>C::SWAGGER_W_LABEL_WAREHOUSE],
    normalizationContext: [C::GROUPS=>[C::R_WAREHOUSE],C::SWAGGER_DEFINITION_NAME=>C::SWAGGER_R_LABEL_WAREHOUSE]
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(RangeFilter::class,properties: [C::PROP_STATS_WAREHOUSE])]
#[ApiFilter(SearchFilter::class,properties: [
    "owner"=>C::MATCH_EXACT,
    "owner.username"=>C::MATCH_PARTIAL,
    "product.name"=>C::MATCH_PARTIAL
])]
class NgdWarehouse extends ApiEntityBase
{
    /**
     * SETTING ITEM GET TO APPLY ONLY ON A SINGLE ITEM AND NOT ON THE COLLECTION
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups([C::ITEM_GET_PRODUCT_LINE,"warehouse:read","warehouse:write","user:item:get","user:write"])]
    #[SerializedName("stock")]
    private ?int $countStock = 0;

    /**
     * @ORM\ManyToOne(targetEntity=NgdProductLine::class, inversedBy="warehouses")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"warehouse:read","warehouse:write","user:write"})
     * @Assert\Valid()
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="warehouses")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups([C::R_PRODUCT_LINE,"warehouse:read","warehouse:write"])]
    #[Assert\Valid()]
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    #[Groups([C::R_PRODUCT_LINE,"warehouse:read","warehouse:write","user:item:get","user:write"])]
    private $label;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    #[Groups([C::R_PRODUCT_LINE,"warehouse:read","warehouse:write","user:item:get","user:write"])]
    private string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups("user:item:get")]
    #[SerializedName("picture")]
    private ?string $fileName = null;

    /**
     * @ORM\Column(type="datetime_immutable")
     */

    private DateTimeInterface  $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        parent::__construct();
    }

    public function getCountStock(): ?int
    {
        return $this->countStock;
    }

    public function setCountStock(?int $countStock): self
    {
        $this->countStock = $countStock;

        return $this;
    }

    public function getProduct(): ?NgdProductLine
    {
        return $this->product;
    }

    public function setProduct(?NgdProductLine $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[Groups(["user:item:get"])]
    #[SerializedName("created.at")]
    public function getCreatedAtAgo(): string {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }
}
