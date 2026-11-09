<?php

namespace App\Tests\Entity;

use App\Entity\Measurement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MeasurementTest extends TestCase
{
    #[DataProvider('dataGetFahrenheit')]
    public function testGetFahrenheit(string $celsius, string $expectedFahrenheit): void
    {
        $measurement = new Measurement();
        $measurement->setCelsius((float) $celsius);
        $this->assertEqualsWithDelta(
            (float) $expectedFahrenheit,
            $measurement->getFahrenheit(),
            0.001
        );
    }

    public static function dataGetFahrenheit(): array
    {
        return [
            ['0', '32'],
            ['-100', '-148'],
            ['100', '212'],
            ['0.5', '32.9'],
            ['12.5', '54.5'],
            ['-10.5', '13.1'],
            ['37', '98.6'],
            ['-40', '-40'],
            ['25', '77'],
            ['15.5', '59.9'],
        ];
    }
}
