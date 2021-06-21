<?php

namespace Adi\JsonRpc;

use Adi\JsonRpc\Responses\ErrorResponse;
use Adi\JsonRpc\Responses\ResultResponse;
use ErrorException;


class Client
{
    /** @var string */
    const VERSION = '2.0';

    /** @var array */
    private $requests;

    public function __construct()
    {
        $this->reset();
    }


    public function reset()
    {
        $this->requests = [];
    }


    public function query($id, string $method, array $arguments = null): self
    {
        $request = self::getRequest($method, $arguments);
        $request['id'] = $id;

        $this->requests[] = $request;

        return $this;
    }


    public function notify($method, array $arguments = null): self
    {
        $request = self::getRequest($method, $arguments);

        $this->requests[] = $request;

        return $this;
    }


    public function encode()
    {
        $input = $this->preEncode();

        if ($input === null) {
            return null;
        }

        return json_encode($input);
    }


    public function preEncode()
    {
        $n = count($this->requests);

        if ($n === 0) {
            return null;
        }

        if ($n === 1) {
            $input = $this->requests[0];
        } else {
            $input = $this->requests;
        }

        $this->reset();

        return $input;
    }


    public function decode(string $json)
    {
        $input = json_decode($json, true);

        $errorCode = json_last_error();

        if ($errorCode !== 0) {
            $errorMessage = json_last_error_msg();
            $jsonException = new ErrorException($errorMessage, $errorCode);

            $valueText = self::getValueText($json);
            throw new ErrorException("Invalid JSON: {$valueText}", 0, E_ERROR, __FILE__, __LINE__, $jsonException);
        }

        return $this->postDecode($input);
    }


    public function postDecode($input)
    {
        if (!$this->getResponses($input, $responses)) {
            $valueText = self::getValueText($input);
            throw new ErrorException("Invalid JSON-RPC 2.0 response: {$valueText}");
        }

        return $responses;
    }

    private static function getRequest(string $method, array $arguments = null): array
    {
        $request = [
            'jsonrpc' => self::VERSION,
            'method' => $method
        ];

        if ($arguments !== null) {
            $request['params'] = $arguments;
        }

        return $request;
    }

    private static function getValueText($value): string
    {
        if (is_null($value)) {
            return 'null';
        }

        if (is_resource($value)) {
            $type = get_resource_type($value);
            $id = (int)$value;
            return "{$type}#{$id}";
        }

        return var_export($value, true);
    }

    private function getResponses($input, array &$responses = null): bool
    {
        if ($this->getResponse($input, $response)) {
            $responses = [$response];
            return true;
        }

        return $this->getBatchResponses($input, $responses);
    }

    private function getResponse($input, &$response)
    {
        return $this->getResultResponse($input, $response) ||
            $this->getErrorResponse($input, $response);
    }

    private function getResultResponse($input, &$response)
    {
        if (
            is_array($input) &&
            !array_key_exists('error', $input) &&
            $this->getVersion($input) &&
            $this->getId($input, $id) &&
            $this->getResult($input, $value)
        ) {
            $response = new ResultResponse($id, $value);
            return true;
        }

        return false;
    }

    private function getVersion(array $input)
    {
        return isset($input['jsonrpc']) && ($input['jsonrpc'] === self::VERSION);
    }

    private function getId(array $input, &$id)
    {
        if (array_key_exists('id', $input)) {
            $id = $input['id'];
            return is_null($id) || is_int($id) || is_float($id) || is_string($id);
        }

        return false;
    }

    private function getResult(array $input, &$value)
    {
        if (array_key_exists('result', $input)) {
            $value = $input['result'];
            return true;
        }

        return false;
    }

    private function getErrorResponse(array &$input, &$response)
    {
        if (
            is_array($input) &&
            !array_key_exists('result', $input) &&
            $this->getVersion($input) &&
            $this->getId($input, $id) &&
            $this->getError($input, $code, $message, $data)
        ) {
            $response = new ErrorResponse($id, $message, $code, $data);
            return true;
        }

        return false;
    }

    private function getError(array $input, &$code, &$message, &$data)
    {
        $error = $input['error'] ?? null;

        return is_array($error) &&
            $this->getErrorCode($error, $code) &&
            $this->getErrorMessage($error, $message) &&
            $this->getErrorData($error, $data);
    }

    private function getErrorCode(array $input, &$code)
    {
        $code = $input['code'] ?? null;

        return is_int($code);
    }

    private function getErrorMessage(array $input, &$message)
    {
        $message = $input['message'] ?? null;

        return is_string($message);
    }

    private function getErrorData(array $input, &$data)
    {
        $data = $input['data'] ?? null;

        return true;
    }

    private function getBatchResponses($input, &$responses)
    {
        if (!is_array($input)) {
            return false;
        }

        $responses = [];
        $i = 0;

        foreach ($input as $key => $value) {
            if ($key !== $i++) {
                return false;
            }

            if (!$this->getResponse($value, $responses[])) {
                return false;
            }
        }

        return true;
    }
}