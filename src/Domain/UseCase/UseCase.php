<?php

namespace App\Domain\UseCase;

use App\Domain\DomainException\DomainException;
use App\Domain\DomainException\InvalidInput;
use Exception;

abstract class UseCase
{
    public function __construct()
    {
        $this->validateData();
    }

    abstract public function execute();

    abstract protected function validateData();

    private function execInputValidations(): void
    {
        try {
            $this->validateData();
        } catch (Exception $exception) {
            if ($exception instanceof DomainException) {
                throw $exception;
            }
            throw new InvalidInput($exception->getMessage());
        }
    }
}
