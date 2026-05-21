<?php

declare(strict_types=1);

namespace Wiring\Http;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use UnexpectedValueException;
use Wiring\Http\Exception\BadRequestException;
use Wiring\Http\Exception\ErrorHandler;
use Wiring\Interfaces\ErrorHandlerInterface;

/**
 * Handles a server request and produces a response.
 *
 * An HTTP request handler process an HTTP request in
 * order to produce an HTTP response.
 */
class RequestHandler implements RequestHandlerInterface
{
    public const KEY = 'key';
    public const AFTER = 'after';
    public const MIDDLEWARE = 'middleware';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /** @var list<array{key: string|null, middleware: MiddlewareInterface, after: bool}> */
    protected $middleware = [];

    /**
     * @var int
     */
    protected $currentMiddleware = -1;

    /**
     * @var int
     */
    protected $afterMiddleware = -1;

    /**
     * @var bool
     */
    protected $isAfterMiddleware = false;

    /**
     * @var bool
     */
    protected $finished = false;

    /**
     * @var bool
     */
    protected $isErrorHandler = false;

    /**
     * RequestHandler constructor.
     *
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(
        ContainerInterface $container,
        ServerRequestInterface $request,
        ResponseInterface $response,
        bool $errorHandler = false
    ) {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
        $this->isErrorHandler = $errorHandler;
    }

    /**
     * Request handling is explicit through handle(), run(), and stop().
     */
    public function __destruct()
    {
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws ErrorHandler
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // Get request and select the next middleware for the active phase.
            $this->request = $request;

            // Check error handler
            if ($this->isErrorHandler) {
                throw new BadRequestException();
            }

            $middlewarePosition = $this->nextMiddlewarePosition($this->isAfterMiddleware);

            // Stop if there isn't any executable middleware remaining.
            if ($middlewarePosition === null) {
                return $this->response;
            }

            // Get and execute the next middleware
            $middlewareArray = $this->middleware[$middlewarePosition];
            $this->executeMiddleware($middlewareArray);
        } catch (Throwable $e) {
            // Call error handler
            return $this->errorHandler($e, $this->request, $this->response);
        } finally {
            $this->finished = true;
        }

        return $this->response;
    }

    /**
     * Get middleware.
     *
     * @param string $key
     *
     * @return null|MiddlewareInterface
     */
    public function getMiddleware(string $key): ?MiddlewareInterface
    {
        $position = $this->findMiddleware($key);
        if ($position === null) {
            return null;
        }

        $middleware = $this->middleware[$position][self::MIDDLEWARE];

        return $middleware;
    }

    /**
     * @param MiddlewareInterface $middleware
     *
     * @param string|null $key
     *
     * @return RequestHandler
     */
    public function addMiddleware(
        MiddlewareInterface $middleware,
        ?string $key = null
    ): RequestHandler {
        // Set middleware array
        $this->middleware[] = [
            self::KEY => $key,
            self::MIDDLEWARE => $middleware,
            self::AFTER => false,
        ];

        return $this;
    }

    /**
     * Find middleware.
     *
     * @param string $key
     *
     * @return null|int
     */
    protected function findMiddleware(string $key): ?int
    {
        foreach ($this->middleware as $k => $middleware) {
            if ($middleware[self::KEY] === $key) {
                return $k;
            }
        }

        return null;
    }

    /**
     * Find the next middleware registered for the active phase.
     */
    protected function nextMiddlewarePosition(bool $after): ?int
    {
        $position = $after ? $this->afterMiddleware : $this->currentMiddleware;

        do {
            $position++;
        } while (isset($this->middleware[$position]) && $this->middleware[$position][self::AFTER] !== $after);

        if (isset($this->middleware[$position]) === false) {
            return null;
        }

        if ($after) {
            $this->afterMiddleware = $position;
        } else {
            $this->currentMiddleware = $position;
        }

        return $position;
    }

    /**
     * Execute a middleware.
     *
     * @param array{key: string|null, middleware: MiddlewareInterface, after: bool} $middlewareArray
     */
    protected function executeMiddleware(array $middlewareArray): void
    {
        $this->response = ($middlewareArray[self::MIDDLEWARE])
            ->process($this->request, $this);
    }

    /**
     * Add router middleware.
     *
     * @param MiddlewareInterface $router
     *
     * @return self
     */
    public function addRouterMiddleware(
        MiddlewareInterface $router
    ): RequestHandler {
        $this->addMiddleware($router, 'router');

        return $this;
    }

    /**
     * Add RequestHandler middleware.
     *
     * @param MiddlewareInterface $RequestHandler
     *
     * @return self
     */
    public function addRequestHandler(
        MiddlewareInterface $RequestHandler
    ): RequestHandler {
        $this->addMiddleware($RequestHandler, 'RequestHandler');

        return $this;
    }

    /**
     * Add emitter middleware.
     *
     * @param MiddlewareInterface $emitter
     *
     * @return self
     */
    public function addEmitterMiddleware(
        MiddlewareInterface $emitter
    ): RequestHandler {
        $this->addAfterMiddleware($emitter, 'emitter');

        return $this;
    }

    /**
     * Added after middleware.
     *
     * @param MiddlewareInterface $middleware
     * @param string $key
     *
     * @return self
     */
    public function addAfterMiddleware(
        MiddlewareInterface $middleware,
        ?string $key
    ): RequestHandler {
        // Set middleware array
        $this->middleware[] = [
            self::KEY => $key,
            self::MIDDLEWARE => $middleware,
            self::AFTER => true,
        ];

        return $this;
    }

    /**
     * Remove middleware.
     *
     * @param string $key
     *
     * @return self
     */
    public function removeMiddleware($key): RequestHandler
    {
        $key = $this->findMiddleware($key);

        if ($key !== null) {
            array_splice($this->middleware, $key, 1);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isAfterMiddleware(): bool
    {
        return $this->isAfterMiddleware;
    }

    /**
     * @param bool $isAfterMiddleware
     *
     * @return self
     */
    public function setIsAfterMiddleware(bool $isAfterMiddleware = true): RequestHandler
    {
        $this->isAfterMiddleware = $isAfterMiddleware;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * Error handler.
     *
     * @param Exception|Throwable $error
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @throws ErrorHandler
     *
     * @return ResponseInterface
     */
    protected function errorHandler(
        Throwable $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        // Checks if error handler is implemented
        if (!$this->container->has(ErrorHandlerInterface::class)) {
            $response->getBody()->write(ErrorHandler::DEFAULT_MESSAGE);

            return $response->withStatus(500);
        }

        $errorHandler = $this->container->get(ErrorHandlerInterface::class);

        if (!is_callable($errorHandler)) {
            throw new UnexpectedValueException('Error handler service must be callable.');
        }

        $response = $errorHandler($request, $response, $error);

        if (!$response instanceof ResponseInterface) {
            throw new UnexpectedValueException('Error handler service must return a response.');
        }

        return $response;
    }
}
