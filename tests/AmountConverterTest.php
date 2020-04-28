<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use Voronkovich\RaiffeisenBankAcquiring\AmountConverter;
use PHPUnit\Framework\TestCase;

class AmountConverterTest extends TestCase
{
    public function testConvertsFromMinorToFormattedValue()
    {
        $amountConverter = new AmountConverter('.');

        $this->assertEquals('0.00', $amountConverter->minorToFormatted(0));
        $this->assertEquals('0.01', $amountConverter->minorToFormatted(1));
        $this->assertEquals('0.10', $amountConverter->minorToFormatted(10));
        $this->assertEquals('0.99', $amountConverter->minorToFormatted(99));
        $this->assertEquals('123.45', $amountConverter->minorToFormatted(12345));
    }

    public function testConvertsFromFormattedToMinorValue()
    {
        $amountConverter = new AmountConverter(',');

        $this->assertEquals(0, $amountConverter->formattedToMinor('0,00'));
        $this->assertEquals(1, $amountConverter->formattedToMinor('0,01'));
        $this->assertEquals(10, $amountConverter->formattedToMinor('0,10'));
        $this->assertEquals(99, $amountConverter->formattedToMinor('0,99'));
        $this->assertEquals(12345, $amountConverter->formattedToMinor('123,45'));
    }
}
