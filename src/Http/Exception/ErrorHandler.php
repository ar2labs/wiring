<?php

declare(strict_types=1);

namespace Wiring\Http\Exception;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Wiring\Interfaces\ErrorHandlerInterface;

class ErrorHandler extends \Exception implements ErrorHandlerInterface
{
    // Define constants error
    const ERROR_CODE = 'code';
    const ERROR_TYPE = 'type';
    const ERROR_STATUS = 'status';
    const ERROR_TITLE = 'title';
    const ERROR_MESSAGE = 'message';
    const ERROR_DATA = 'data';
    const ERROR_FILE = 'file';
    const ERROR_LINE = 'line';
    const ERROR_TRACE = 'trace';
    const ERROR_DEBUG = 'debug';

    // Define types & messages
    const CONTENT_TYPE = 'Content-Type';
    const APP_JSON = 'application/json';
    const APP_HTML = 'text/html';
    const DEBUG_MODE = 'Debug-Mode';
    const UNDEFINED_MESSAGE = 'Undefined error message.';
    const DEFAULT_MESSAGE = 'A website error has occurred. Sorry for the temporary inconvenience.';
    const DEBUG_MESSAGE = 'The application could not run because of the following error:';

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Throwable
     */
    protected $exception;

    /**
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * @var array
     */
    protected $loggerContext = [];

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
     * @param ResponseInterface      $response
     * @param Throwable              $exception
     * @param LoggerInterface|null   $logger
     * @param array                  $loggerContext
     * @param bool                   $debug
     */
    public function __construct(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Throwable $exception,
        LoggerInterface $logger = null,
        array $loggerContext = [],
        bool $debug = false
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->exception = $exception;
        $this->logger = $logger;
        $this->loggerContext = $loggerContext;
        $this->debug = $debug;

        parent::__construct(
            $exception->getMessage() ?? self::UNDEFINED_MESSAGE,
            $exception->getCode() ?? 0,
            $exception
        );
    }

    /**
     * Return an error into an HTTP or JSON data array.
     *
     * @param string $message
     *
     * @return array
     */
    public function error(?string $message = null): array
    {
        $type = $this->request->getHeader(self::CONTENT_TYPE);
        $mode = $this->request->getHeader(self::DEBUG_MODE);

        if ($message == null) {
            $message = $this->debug ? self::DEBUG_MESSAGE : self::DEFAULT_MESSAGE;
        }

        $msg = $this->exception->getMessage() ?? $message;
        $code = $this->exception->getCode() ?? 0;

        // Debug mode header
        if ((isset($mode[0])) && ($mode[0] == '0' || $mode[0] == '1')) {
            $this->debug = (bool) $mode[0];
        }

        $statusCode = method_exists($this->exception, 'getStatusCode') ?
            $this->exception->getStatusCode() : ($code >= 100 && $code <= 500 ? $code : 400);

        $this->response->withStatus($statusCode);

        if ($this->logger instanceof LoggerInterface) {
            $this->logger->error($this->exception->getMessage(), $this->loggerContext);
        }

        $this->isJson = isset($type[0]) && $type[0] == self::APP_JSON;

        if ($this->isJson) {
            // Define content-type to json
            $this->response->withHeader(self::CONTENT_TYPE, self::APP_JSON);

            $error = [
                self::ERROR_CODE => $statusCode,
                self::ERROR_STATUS => 'error',
                self::ERROR_MESSAGE => $msg,
                self::ERROR_DATA => [],
            ];

            // Debug mode header
            if (is_array($mode) && ($mode[0] == '1')) {
                // Debug details
                $error[self::ERROR_DATA] = [
                    self::ERROR_CODE => $code,
                    self::ERROR_MESSAGE => $this->exception->getTraceAsString(),
                    self::ERROR_FILE => $this->exception->getFile(),
                    self::ERROR_LINE => $this->exception->getLine(),
                ];
            }

            return $error;
        }

        // Define content-type to html
        $this->response->withHeader(self::CONTENT_TYPE, self::APP_HTML);
        $message = sprintf('<span>%s</span>', htmlentities($msg));

        $error = [
            self::ERROR_CODE => $statusCode,
            self::ERROR_TYPE => get_class($this->exception),
            self::ERROR_MESSAGE => $message,
        ];

        // Debug mode
        if ($this->debug) {
            $trace = $this->exception->getTraceAsString();
            $trace = sprintf('<pre>%s</pre>', htmlentities($trace));

            $error[self::ERROR_MESSAGE] = $message;
            $error[self::ERROR_CODE] = $statusCode;
            $error[self::ERROR_FILE] = $this->exception->getFile();
            $error[self::ERROR_LINE] = $this->exception->getLine();
            $error[self::ERROR_TRACE] = $trace;
        }

        $error[self::ERROR_DEBUG] = $this->debug;
        $error[self::ERROR_TITLE] = $message;

        return $error;
    }

    /**
     * Get exception.
     *
     * @return Throwable|null
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
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
