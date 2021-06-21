<?php

namespace Adi\JsonRpc;

interface Evaluator
{
    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function evaluate($method, $arguments);
}