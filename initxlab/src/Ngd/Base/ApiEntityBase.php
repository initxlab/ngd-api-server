<?php


namespace Initxlab\Ngd\Base;


use Doctrine\ORM\Mapping as ORM;
use Initxlab\Ngd\Contract\ApiEntityInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

class ApiEntityBase implements ApiEntityInterface
{
    /**
     * The id is generated from the request side
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    protected UuidV4 $id;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    /**
     * @return UuidV4
     */
    public function getId(): UuidV4
    {
        return $this->id;
    }
}