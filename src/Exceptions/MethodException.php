<?php

namespace Adi\JsonRpc\Exceptions;

use Adi\JsonRpc\Responses\ErrorResponse;


class MethodException extends Exception
{
    public function __construct()
    {
        parent::__construct('Method not found', ErrorResponse::INVALID_METHOD);
    }
}