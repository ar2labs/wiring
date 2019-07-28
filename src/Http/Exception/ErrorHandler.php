<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class ErrorHandler extends \Exception
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var \Exception|\Throwable
     */
    protected $exception;

    /**
     * @var \Psr\Log\LoggerInterface;
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $isJson = false;

    /**
     * Create error handler.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     * @param \Exception|\Throwable $exception
     * @param bool $debug
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response,
                                $exception, LoggerInterface $logger = null, $debug = false)
    {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
        $this->logger = $logger;
        $this->debug = $debug;

        $msg  = $this->exception->getMessage() ?? 'An error has occurred.';
        $code = $this->exception->getCode() ?? 0;

        parent::__construct($msg, $code, $exception);
    }

    /**
     * Get exception.
     *
     * @return \Exception|\Throwable
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Return an error into an HTTP or JSON data array.
     *
     * @param string $title
     *
     * @return array
     */
    public function error(?string $title = null): array
    {
        if ($title == null) {
            $title = $this->debug ?
                'The application could not run because of the following error:' :
                'A website error has occurred. Sorry for the temporary inconvenience.';
        }

        $type = $this->request->getHeader('Content-Type');
        $mode = $this->request->getHeader('Debug-Mode');
        $msg  = $this->exception->getMessage() ?? $title;
        $code = $this->exception->getCode() ?? 0;

        // Debug mode header
        if ((isset($mode[0])) && ($mode[0] == '0' || $mode[0] == '1')) {
            $this->debug = (bool)$mode[0];
        }

        $statusCode = method_exists($this->exception, 'getStatusCode') ? $this->exception->getStatusCode() : null;

        // Check status code is null
        if ($statusCode == null) {
            $statusCode = $code >= 100 && $code <= 500 ? $code : 400;
        }

        $this->response->withStatus($statusCode);

        // Check logger exist
        if ($this->logger !== null) {
            // Send error to log
            $this->logger->error($this->exception->getMessage());
        }

        $this->isJson = isset($type[0]) && $type[0] == 'application/json';

        // Check content-type is application/json
        if ($this->isJson) {
            // Define content-type to json
            $this->response->withHeader('Content-Type', 'application/json');

            $error = [
                'code' => $statusCode,
                'status' => 'error',
                'message' => $msg,
                'data' => []
            ];

            // Debug mode header
            if (is_array($mode) && ($mode[0] == '1')) {
                $error['data'] = [
                    'code' => $code,
                    'error' => $this->exception->getTraceAsString(),
                    'file' => $this->exception->getFile(),
                    'line' => $this->exception->getLine(),
                ];
            }

            return $error;
        }

        // Define content-type to html
        $this->response->withHeader('Content-Type', 'text/html');
        $message = sprintf('<span>%s</span>', htmlentities($msg));

        $error = [
            'code' => $statusCode,
            'type' => get_class($this->exception),
            'message' => $title
        ];

        // Debug mode
        if ($this->debug) {
            $trace = $this->exception->getTraceAsString();
            $trace = sprintf('<pre>%s</pre>', htmlentities($trace));

            $error['message'] = $message;
            $error['file'] = $this->exception->getFile();
            $error['line'] = $this->exception->getLine();
            $error['code'] = $statusCode;
            $error['trace'] = $trace;
        }

        $error['debug'] = $this->debug;
        $error['title'] = $title;

        return $error;
    }

    /**
     * Check is JSON.
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }
}
