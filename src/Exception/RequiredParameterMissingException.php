<?php

declare(strict_types=1);

namespace Voronkovich\RaiffeisenBankAcquiring\Exception;

class RequiredParameterMissingException extends \Exception implements ExceptionInterface
{
    public function __construct(string $parameter)
    {
        parent::__construct(\sprintf('Required parameter "%s" is missing.', $parameter));
    }
}
