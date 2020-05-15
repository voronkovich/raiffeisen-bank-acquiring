<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use PHPUnit\Framework\TestCase;
use Voronkovich\RaiffeisenBankAcquiring\Exception\InvalidArgumentException;
use Voronkovich\RaiffeisenBankAcquiring\MerchantIdFormatter;

/**
 * @author Oleg Voronkovich <oleg-voronkovich@yandex.ru>
 */
class MerchantIdFormatterTest extends TestCase
{
    public function testFormatsMerchantIdForPaymentData()
    {
        $merchantId = MerchantIdFormatter::format('1680024001', '80024001');

        $this->assertEquals('000001680024001-80024001', $merchantId);
    }

    public function testParsesMechantIdAndTerminalId()
    {
        [ $merchantId, $terminalId ] = MerchantIdFormatter::parse('000001680024001-80024001');

        $this->assertEquals('1680024001', $merchantId);
        $this->assertEquals('80024001', $terminalId);
    }

    public function testThrowsExceptionIfParsingValudHasInvalidFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value "00111-111" has invalid format. Expected format: "00000%d-%d".');

        $result = MerchantIdFormatter::parse('00111-111');
    }
}
