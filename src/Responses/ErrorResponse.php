<?php

namespace Adi\JsonRpc\Responses;


class ErrorResponse extends Response
{
    const PARSE_ERROR = -32700;
    const INVALID_ARGUMENTS = -32602;
    const INVALID_METHOD = -32601;
    const INVALID_REQUEST = -32600;

    /** @var string */
    private $message;

    /** @var int */
    private $code;

    /** @var mixed */
    private $data;


    public function __construct($id, string $message, int $code, $data = null)
    {
        parent::__construct($id);

        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getData()
    {
        return $this->data;
    }
}