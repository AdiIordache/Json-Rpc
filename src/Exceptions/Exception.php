<?php



namespace Adi\JsonRpc\Exceptions;

use Exception as BaseException;


abstract class Exception extends BaseException
{
    /** @var null|boolean|integer|float|string|array */
    private $data;

    public function __construct($message, $code, $data = null)
    {
        parent::__construct($message, $code);

        $this->data = $data;
    }

    /**
     * @return null|boolean|integer|float|string|array
     * Returns the (optional) data property of the error object.
     */
    public function getData()
    {
        return $this->data;
    }
}