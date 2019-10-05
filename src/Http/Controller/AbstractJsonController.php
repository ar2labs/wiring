<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use Wiring\Http\Exception\ErrorHandler;
use Wiring\Http\Exception\HttpException;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Interfaces\RouteInterface;

abstract class AbstractJsonController extends AbstractController
{
    /**
     * Create container and response interface.
     *
     * @param ContainerInterface $container
     * @param ResponseInterface $response
     */
    public function __construct(
        ContainerInterface $container,
        ResponseInterface $response
    ) {
        $this->addDefaultResponseHeader('content-type', 'application/json');

        parent::__construct($container, $response);
    }

    /**
     * Invoke the route callable based on the abstract strategy.
     *
     * @param RouteInterface         $route
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function invokeRouteCallable(
        RouteInterface $route,
        ServerRequestInterface $request
    ): ResponseInterface {
        $controller = $route->getCallable($this->getContainer());
        $response = $controller($request, $route->getVars());

        if ($this->isJsonEncodable($response)) {
            $body = json_encode($response);
            $response->getBody()->write($body);
        }

        return $this->applyDefaultResponseHeaders($response);
    }

    /**
     * Check if the response can be converted to JSON.
     *
     * Arrays can always be converted, objects can be
     * converted if they're not a response already
     *
     * @param mixed $response
     *
     * @return bool
     */
    protected function isJsonEncodable($response): bool
    {
        if ($response instanceof ResponseInterface) {
            return false;
        }

        return (is_array($response) || is_object($response));
    }

    /**
     * Get JSON Strategy.
     *
     * @return JsonStrategyInterface
     */
    public function json(): JsonStrategyInterface
    {
        return $this->get(JsonStrategyInterface::class);
    }

    /**
     * Get a middleware that will decorate a NotFoundException.
     *
     * @param NotFoundException $exceptionNotFound
     *
     * @return MiddlewareInterface
     */
    public function getNotFoundDecorator(
        NotFoundException $exceptionNotFound
    ): MiddlewareInterface {
        return $this->buildJsonResponseMiddleware($exceptionNotFound);
    }

    /**
     * Get a middleware that will decorate a NotAllowedException.
     *
     * @param MethodNotAllowedException $exception
     *
     * @return MiddlewareInterface
     */
    public function getMethodNotAllowedDecorator(
        MethodNotAllowedException $exception
    ): MiddlewareInterface {
        return $this->buildJsonResponseMiddleware($exception);
    }

    /**
     * Return a middleware the creates a JSON response from an HTTP exception.
     *
     * @param HttpException $exception
     *
     * @return MiddlewareInterface
     */
    protected function buildJsonResponseMiddleware(
        HttpException $exception
    ): MiddlewareInterface {
        return new class($this->response, $exception) implements MiddlewareInterface {
            protected $response;
            protected $exception;

            public function __construct(
                ResponseInterface $response,
                HttpException $exception
            ) {
                $this->response = $response;
                $this->exception = $exception;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                return $this->exception->buildJsonResponse($this->response);
            }
        };
    }

    /**
     * Get a middleware that acts as a throwable handler,
     * it should wrap the rest of the middleware stack
     * and catch any throwables.
     *
     * @return MiddlewareInterface
     */
    public function getThrowableHandler(): MiddlewareInterface
    {
        if ($this->container instanceof ContainerInterface) {
            // Return throwable handler
            return new class($this->container, $this->response) implements MiddlewareInterface {
                protected $container;
                protected $response;

                public function __construct(ContainerInterface $container, ResponseInterface $response)
                {
                    $this->container = $container;
                    $this->response = $response;
                }

                public function process(
                    ServerRequestInterface $request,
                    RequestHandlerInterface $handler
                ): ResponseInterface {
                    try {
                        return $handler->handle($request);
                    } catch (Throwable $e) {
                        // Call error handler
                        return $this->errorHandler($e, $request, $this->response);
                    }
                }

                /**
                 * Error handler.
                 *
                 * @param Throwable $error
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
                    // Create new error handler
                    $errorHandler = new ErrorHandler($request, $response, $error);
                    $error = $errorHandler->error();

                    $response->getBody()->write((string) json_encode([
                        'code' => 500,
                        'status' => 'error',
                        'message' => $error['message'],
                        'data' => [],
                    ]));

                    return $response;
                }
            };
        }
    }
}
