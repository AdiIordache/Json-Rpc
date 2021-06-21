<?php

namespace Adi\JsonRpc\Exceptions;

use Adi\JsonRpc\Responses\ErrorResponse;

class ArgumentException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid params', ErrorResponse::INVALID_ARGUMENTS);
    }
}