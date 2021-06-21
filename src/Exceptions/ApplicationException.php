<?php

namespace Adi\JsonRpc\Exceptions;

class ApplicationException extends Exception
{

    public function __construct($message, $code, $data = null)
    {
        if (!self::isValidMessage($message)) {
            $message = '';
        }

        if (!self::isValidCode($code)) {
            $code = 1;
        }

        if (!self::isValidData($data)) {
            $data = null;
        }

        parent::__construct($message, $code, $data);
    }

    private static function isValidMessage($input)
    {
        return is_string($input);
    }

    private static function isValidCode($code)
    {
        return is_int($code) && (($code < -32768) || (-32000 < $code));
    }


    private static function isValidData($input)
    {
        $type = gettype($input);

        return ($type === 'array')
            || ($type === 'string')
            || ($type === 'double')
            || ($type === 'integer')
            || ($type === 'boolean')
            || ($type === 'NULL');
    }
}