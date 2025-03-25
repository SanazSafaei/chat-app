<?php

declare(strict_types=1);

namespace App\Domain\DomainException;

use Exception;

abstract class DomainException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?? $this->getMessage() , 400);
    }
}
