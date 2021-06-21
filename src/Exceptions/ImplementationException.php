<?php



namespace Adi\JsonRpc\Exceptions;


class ImplementationException extends Exception
{

    public function __construct($code, $data = null)
    {
        if (!self::isValidCode($code)) {
            $code = -32099;
        }

        if (!self::isValidData($data)) {
            $data = null;
        }

        parent::__construct('Server error', $code, $data);
    }


    private static function isValidCode($code)
    {
        return is_int($code) && (-32099 <= $code) && ($code <= -32000);
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