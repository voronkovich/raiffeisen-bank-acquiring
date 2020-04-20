<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Tests;

use Voronkovich\RaiffeisenBankAcquiring\CallbackResponse;
use PHPUnit\Framework\TestCase;

class CallbackResponseTest extends TestCase
{
    public function testCreatesSuccessResponse()
    {
        $response = CallbackResponse::success();

        $this->assertEquals('RESP_CODE(0)', (string) $response);
    }

    public function testCreatesAlreadyHandledResponse()
    {
        $response = CallbackResponse::alreadyHandled();

        $this->assertEquals('RESP_CODE(1)', (string) $response);
    }

    public function testCreatesTemporaryUnavailableResponse()
    {
        $response = CallbackResponse::temporaryUnavailable();

        $this->assertEquals('RESP_CODE(-1)', (string) $response);
    }

    public function testCreatesErrorResponse()
    {
        $response = CallbackResponse::error();

        $this->assertEquals('RESP_CODE(-2)', (string) $response);
    }
}
