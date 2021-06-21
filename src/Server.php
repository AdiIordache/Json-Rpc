<?php

namespace Adi\JsonRpc;

use Adi\JsonRpc\Exceptions\Exception;
use Adi\JsonRpc\Responses\ErrorResponse;


class Server
{
    /** @var string */
    const VERSION = '2.0';

    /** @var Evaluator */
    private $evaluator;

    /**
     * @param Evaluator $evaluator
     */
    public function __construct(Evaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }


    public function reply($json)
    {
        if (is_string($json)) {
            $input = json_decode($json, true);
        } else {
            $input = null;
        }

        $reply = $this->rawReply($input);

        if (is_array($reply)) {
            $output = json_encode($reply);
        } else {
            $output = null;
        }

        return $output;
    }


    public function rawReply($input)
    {
        if (is_array($input)) {
            $reply = $this->processInput($input);
        } else {
            $reply = $this->parseError();
        }

        return $reply;
    }


    private function processInput(array $input)
    {
        if (count($input) === 0) {
            return $this->requestError();
        }

        if (isset($input[0])) {
            return $this->processBatchRequests($input);
        }

        return $this->processRequest($input);
    }


    private function processBatchRequests($input)
    {
        $replies = array();

        foreach ($input as $request) {
            $reply = $this->processRequest($request);

            if ($reply !== null) {
                $replies[] = $reply;
            }
        }

        if (count($replies) === 0) {
            return null;
        }

        return $replies;
    }


    private function processRequest($request)
    {
        if (!is_array($request)) {
            return $this->requestError();
        }

        // The presence of the 'id' key indicates that a response is expected
        $isQuery = array_key_exists('id', $request);

        $id = &$request['id'];

        if (($id !== null) && !is_int($id) && !is_float($id) && !is_string($id)) {
            return $this->requestError();
        }

        $version = &$request['jsonrpc'];

        if ($version !== self::VERSION) {
            return $this->requestError($id);
        }

        $method = &$request['method'];

        if (!is_string($method)) {
            return $this->requestError($id);
        }

        // The 'params' key is optional, but must be non-null when provided
        if (array_key_exists('params', $request)) {
            $arguments = $request['params'];

            if (!is_array($arguments)) {
                return $this->requestError($id);
            }
        } else {
            $arguments = array();
        }

        if ($isQuery) {
            return $this->processQuery($id, $method, $arguments);
        }

        $this->processNotification($method, $arguments);
        return null;
    }


    private function processQuery($id, $method, $arguments)
    {
        try {
            $result = $this->evaluator->evaluate($method, $arguments);
            return $this->response($id, $result);
        } catch (Exception $exception) {
            $code = $exception->getCode();
            $message = $exception->getMessage();
            $data = $exception->getData();

            return $this->error($id, $code, $message, $data);
        }
    }


    private function processNotification($method, $arguments)
    {
        try {
            $this->evaluator->evaluate($method, $arguments);
        } catch (Exception $exception) {
        }
    }


    private function parseError()
    {
        return $this->error(null, ErrorResponse::PARSE_ERROR, 'Parse error');
    }


    private function requestError($id = null)
    {
        return $this->error($id, ErrorResponse::INVALID_REQUEST, 'Invalid Request');
    }


    private function error($id, $code, $message, $data = null)
    {
        $error = array(
            'code' => $code,
            'message' => $message
        );

        if ($data !== null) {
            $error['data'] = $data;
        }

        return array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'error' => $error
        );
    }


    private function response($id, $result)
    {
        return array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'result' => $result
        );
    }
}