<?php
/*
 * This file is part of the initxlab/ngd-api-server package.
 *
 * (c) Jean "Nemo" M. <initxlab@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        C::F_CSV=>[C::MIME_TXT_CSV]
    ]
],
    denormalizationContext: [C::GROUPS=>[C::W_WAREHOUSE],C::SWAGGER_DEFINITION_NAME=>C::SWAGGER_W_LABEL_WAREHOUSE],
    normalizationContext: [C::GROUPS=>[C::R_WAREHOUSE],C::SWAGGER_DEFINITION_NAME=>C::SWAGGER_R_LABEL_WAREHOUSE]
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(RangeFilter::class,properties: [C::PROP_STATS_WAREHOUSE])]
#[ApiFilter(SearchFilter::class,properties: [
    C::PROP_OWNER=>C::MATCH_EXACT,
    C::PROP_OWNER_USERNAME=>C::MATCH_PARTIAL,
    C::PROP_PRODUCT_NAME=>C::MATCH_PARTIAL
])]
class NgdWarehouse extends ApiEntityBase
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups([C::ITEM_GET_PRODUCT_LINE,C::R_WAREHOUSE,C::W_WAREHOUSE,C::ITEM_GET_USER,C::W_USER])]
    #[SerializedName(C::LABEL_STOCK)]
    private ?int $countStock = 0;

    /**
     * @ORM\ManyToOne(targetEntity=NgdProductLine::class, inversedBy="warehouses")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups([C::R_WAREHOUSE,C::W_WAREHOUSE,C::W_USER])]
    #[Assert\Valid()]
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="warehouses")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups([C::R_PRODUCT_LINE,C::R_WAREHOUSE,C::W_WAREHOUSE])]
    #[Assert\Valid()]
    private $owner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups([C::R_PRODUCT_LINE,C::R_WAREHOUSE,C::W_WAREHOUSE,C::ITEM_GET_USER,C::W_USER])]
    #[Assert\NotBlank()]
    private string $label;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    #[Groups([C::R_PRODUCT_LINE,C::R_WAREHOUSE,C::W_WAREHOUSE,C::ITEM_GET_USER,C::W_USER])]
    #[Assert\NotBlank()]
    private string $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[Groups(C::ITEM_GET_USER)]
    #[SerializedName(C::LABEL_PICTURE)]
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

    #[Groups([C::ITEM_GET_USER])]
    #[SerializedName(C::LABEL_CREATED_AT)]
    public function getCreatedAtAgo(): string {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }
}
