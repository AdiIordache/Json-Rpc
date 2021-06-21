<?php


namespace Adi\JsonRpc\Responses;


class ResultResponse extends Response
{

    private $value;

    public function __construct($id, $value)
    {
        parent::__construct($id);

        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}