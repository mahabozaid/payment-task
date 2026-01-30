<?php

namespace App\Exceptions;

use Exception;
use App\Utils\ApiResponse;

class LogicalException extends Exception
{
    protected int $status;
    protected $errors;

    public function __construct(
        string $message = "",
        int $status = 400,
        $errors = null,
        int $code = 400
    ) {
        parent::__construct($message, $code);
        $this->status = $status;
        $this->errors = $errors;
    }

    public function render()
    {
        return ApiResponse::fail(
            message: $this->getMessage(),
            code: $this->status,
            httpStatus: $this->getCode(),
            errors: $this->errors
        );
    }
}
