<?php


namespace Initxlab\Helper;


trait HelperIntegerTrait
{
    public function integerRandom(int $min, int $max): int
    {
        try {
            return random_int($min, $max);
        } catch (\Exception $e) {
        }
    }
}