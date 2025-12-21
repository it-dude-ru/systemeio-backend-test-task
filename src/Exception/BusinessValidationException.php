<?php

namespace App\Exception;

class BusinessValidationException extends \RuntimeException
{
    public function __construct(
        private string $field,
        string $message
    ) {
        parent::__construct($message);
    }

    public function getField(): string
    {
        return $this->field;
    }
}
