<?php

namespace App\Tests\DataFixtures;

use PHPUnit\Framework\TestCase;

class ImportTest extends TestCase
{
    private const PATH_TO_FIXTURES = "../../config/fixtures/";
    private const DATA_PRODUCT_LINE = "product_lines.json";

    private function dataProductLines(): ?string {
        $file = self::PATH_TO_FIXTURES.self::DATA_PRODUCT_LINE;
        return file_exists($file) ? $file : null;
    }

    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    /**
     * @throws
    */
    public function testProductLinesConfig(): void
    {
        if(null === $this->dataProductLines()) {
            throw new \RuntimeException('Expecting a File ');
        }

        $jsonString = json_decode(file_get_contents($this->dataProductLines()), true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotNull($jsonString);
        $this->assertIsArray($jsonString);
        $this->assertArrayHasKey('data',$jsonString);
    }
}
