<?php

namespace App\DataFixtures;

use App\Entity\NgdProductLine;
use App\Entity\NgdWarehouse;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Faker\Generator;
use Initxlab\Helper\HelperIntegerTrait;
use JsonException;
use RuntimeException;
/**
 * TODO : SUBJECT TO IMPROVEMENT. SHOULD LOAD USERS FIRST ADD SLUGS
 * Class ApiFixtures
 * Designed to fill db with fake data
 * @package App\DataFixtures
 */
class ApiFixtures extends Fixture
{
    use HelperIntegerTrait;

    private const PATH_TO_FIXTURES = "./config/fixtures/";
    private const DATA_PRODUCT_LINE = "product_lines.json";
    private const FILE_IMG_EXTENSIONS = ["jpg","png","gif","png"];
    private const BOOLEANS = [true,false];

    private Generator $faker;
    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadProductLines($manager);
        $manager->flush();
    }

    private function dataProductLines(): ?string
    {
        $file = self::PATH_TO_FIXTURES.self::DATA_PRODUCT_LINE;
        return file_exists($file) ? $file : null;
    }

    private function loadProductLines(ObjectManager $manager): void
    {
        if(null === $this->dataProductLines()) {
            throw new RuntimeException('Expecting a File Name ');
        }

        $decode = null;

        try {
            $decode = json_decode(file_get_contents($this->dataProductLines()), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
        }

        $data = $decode["data"];

        foreach ($data as $idx => $fixtureData) {

            $productLine = new NgdProductLine();
            $productLine->setName($fixtureData["name"]);
            $productLine->setDescription("Stock for product line related to ".$fixtureData["name"]);
            $productLine->setIsPublished($this->faker->randomElement([true, false]));

            $setStock = $this->faker->randomElement(self::BOOLEANS);
            $countOwners = $this->integerRandom(0,25);
            $this->loadUsers($manager,$productLine,$countOwners,$setStock);
            $manager->persist($productLine);
        }
    }

    private function loadUsers(ObjectManager $manager, ?NgdProductLine $productLine, int $max = 20, bool $setStock = false ): void
    {
        if(0 === $max) {
            return;
        }

        for( $i = 0; $i < $max; $i++ ){

            $user = new User();
            $user->setEmail($this->faker->unique()->email());
            $user->setUsername($this->faker->unique()->userName());
            $user->setPassword('test1');

            // product line found we create the stock for the user
            if((true === $setStock) && $productLine instanceof NgdProductLine) {
                $this->loadWarehouses($manager,$user,$productLine);
            }

            $manager->persist($user);

        }
    }

    private function loadWarehouses(ObjectManager $manager, User $owner, NgdProductLine $productLine):void
    {
        $stock = new NgdWarehouse();
        $stock->setDescription($this->faker->text(255));
        try {
            $stock->setProduct($productLine);
            $stock->setOwner($owner);
            $stock->setCountStock($this->integerRandom(0,58));
            $stock->setLabel($productLine->getName() . " " . $this->faker->text($this->integerRandom(5, 150)));
            $setFileName = $this->faker->randomElement(self::BOOLEANS);
            if(true === $setFileName) {
                $stock->setFileName($this->faker->words()."-"
                    .strtolower($productLine->getName()).".".$this->faker->randomElement(self::FILE_IMG_EXTENSIONS));
            }
        } catch (\Exception $e) {
        }
        $manager->persist($stock);
    }

}
