<?php

declare(strict_types=1);

namespace Wiring\Http;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wiring\Interfaces\ApplicationInterface;
use Wiring\Interfaces\ErrorHandlerInterface;
use Wiring\Http\Exception\ErrorHandler;

/**
 * Handles a server request and produces a response.
 *
 * An HTTP request handler process an HTTP request in
 * order to produce an HTTP response.
 */
class RequestHandler implements RequestHandlerInterface
{
    const KEY = 'key';
    const AFTER = 'after';
    const MIDDLEWARE = 'middleware';

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

    /**
     * @var array
     */
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
     * RequestHandler constructor.
     *
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(
        ContainerInterface $container,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;

        // Check container set exist
        if (method_exists($this->container, 'set')) {
            // Inject self application for middlewares freedom
            $this->container->set(ApplicationInterface::class, $this);
        }
    }

    /**
     * Response destructor.
     *
     * @return null|ResponseInterface
     */
    public function __destruct()
    {
        $this->setIsAfterMiddleware();

        if (!$this->isFinished()) {
            $this->handle($this->request);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ErrorHandler
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            // Get request and increment current middleware
            $this->request = $request;
            $this->currentMiddleware++;

            // Stop if there isn't any executable middleware remaining
            if (isset($this->middleware[$this->currentMiddleware]) === false) {
                // Return response
                return $this->response;
            }

            // Get and execute the next middleware
            $this->executeMiddleware($this->middleware[$this->currentMiddleware]);
        } catch (Throwable $e) {
            // Call error handler
            return $this->errorHandler($e, $this->request, $this->response);
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

        return $this->middleware[$position][self::MIDDLEWARE];
    }

    /**
     * @param MiddlewareInterface $middleware
     * @param string|null $key
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
     * @param array $middlewareArray
     */
    protected function executeMiddleware(array $middlewareArray): void
    {
        if ($middlewareArray[self::MIDDLEWARE] instanceof MiddlewareInterface) {
            $this->response = ($middlewareArray[self::MIDDLEWARE])
                ->process($this->request, $this);
        }
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
            unset($this->middleware[$key]);
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
     * @return mixed
     */
    protected function errorHandler(
        $error,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        // Check has handler
        if (!$this->container->has(ErrorHandlerInterface::class)) {
            // Create new error handler
            $errorHandler = new ErrorHandler($request, $response, $error);
            $error = $errorHandler->error();

            return  $response->getBody()->write($error['message']);
        }

        /** @var callable $errorHandler */
        $errorHandler = $this->container->get(ErrorHandlerInterface::class);

        return $errorHandler($request, $response, $error);
    }
}
