<?php

namespace Adi\JsonRpc;

interface Evaluator
{
    public function evaluate(string $method, array $arguments): mixed;
}