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
    public const ERROR_CODE = 'code';
    public const ERROR_TYPE = 'type';
    public const ERROR_STATUS = 'status';
    public const ERROR_TITLE = 'title';
    public const ERROR_MESSAGE = 'message';
    public const ERROR_DATA = 'data';
    public const ERROR_FILE = 'file';
    public const ERROR_LINE = 'line';
    public const ERROR_TRACE = 'trace';
    public const ERROR_DEBUG = 'debug';

    // Define types & messages
    public const CONTENT_TYPE = 'Content-Type';
    public const APP_JSON = 'application/json';
    public const APP_HTML = 'text/html';
    public const DEBUG_MODE = 'Debug-Mode';
    public const UNDEFINED_MESSAGE = 'Undefined error message.';
    public const DEFAULT_MESSAGE = 'A website error has occurred. Sorry for the temporary inconvenience.';
    public const DEBUG_MESSAGE = 'The application could not run because of the following error:';

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

        // Get debug mode from header
        if ((is_array($mode)) && (array_key_exists('0', $mode)) &&
            ($mode[0] == '0' || $mode[0] == '1')) {
            $this->debug = (bool) $mode[0];
        }

        if ($message == null) {
            $message = $this->debug ? self::DEBUG_MESSAGE : self::DEFAULT_MESSAGE;
        }

        // Get message
        $msg = $this->exception->getMessage() ?? $message;

        // Logger
        if ($this->logger instanceof LoggerInterface) {
            $this->logger->error($this->exception->getMessage(), $this->loggerContext);
        }

        if ($this->response->getStatusCode()) {
            // Set the HTTP response code
            http_response_code($this->response->getStatusCode());
        }

        $this->isJson = isset($type[0]) && $type[0] == self::APP_JSON;

        if ($this->isJson) {
            // Return error message to JSON
            return $this->errorJson($msg);
        }

        // Return error message to HTML params
        return $this->errorHtml($msg);
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

    /**
     * Return error message to HTML params.
     *
     * @param string $message
     *
     * @return array
     */
    private function errorHtml(string $message): array
    {
        // Define Content-Type to HTML
        $this->response->withHeader(self::CONTENT_TYPE, self::APP_HTML);

        $message = sprintf('<span>%s</span>', htmlentities($message));

        $error = [
            self::ERROR_TYPE => get_class($this->exception),
            self::ERROR_MESSAGE => $message,
        ];

        // Debug mode
        if ($this->debug) {
            // Debug details
            $trace = $this->exception->getTraceAsString();
            $trace = sprintf('<pre>%s</pre>', htmlentities($trace));

            $error[self::ERROR_MESSAGE] = $message;
            $error[self::ERROR_CODE] = $this->exception->getCode();
            $error[self::ERROR_FILE] = $this->exception->getFile();
            $error[self::ERROR_LINE] = $this->exception->getLine();
            $error[self::ERROR_TRACE] = $trace;
        }

        $error[self::ERROR_DEBUG] = $this->debug;
        $error[self::ERROR_TITLE] = $message;

        return $error;
    }

    /**
     * Return error message to JSON.
     *
     * @param string $message
     *
     * @return array
     */
    private function errorJson(string $message): array
    {
        // Define Content-Type to JSON
        $this->response->withHeader(self::CONTENT_TYPE, self::APP_JSON);

        $error = [
            self::ERROR_STATUS => false,
            self::ERROR_MESSAGE => $message,
        ];

        // Debug mode
        if ($this->debug) {
            // Debug details
            $error[self::ERROR_DATA] = [
                self::ERROR_CODE => $this->exception->getCode() ?? 0,
                self::ERROR_MESSAGE => $this->exception->getTraceAsString(),
                self::ERROR_FILE => $this->exception->getFile(),
                self::ERROR_LINE => $this->exception->getLine(),
            ];
        }

        return $error;
    }
}
