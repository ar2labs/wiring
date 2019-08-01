<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use League\Route\Route;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Wiring\Interfaces\JsonStrategyInterface;
use Wiring\Http\Exception\HttpException;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;

abstract class AbstractJsonController extends AbstractController
{
    /**
     * Create container and response interface.
     *
     * @param ContainerInterface $container
     * @param ResponseFactoryInterface $response
     */
    public function __construct(
        ContainerInterface $container,
        ResponseInterface $response
    ) {
        $this->setContainer($container);
        $this->setResponse($response);
        $this->addDefaultResponseHeader('content-type', 'application/json');
    }

    /**
     * Invoke the route callable based on the strategy.
     *
     * @param Route                  $route
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function invokeRouteCallable(
        Route $route,
        ServerRequestInterface $request
    ): ResponseInterface {
        $controller = $route->getCallable($this->getContainer());
        $response = $controller($request, $route->getVars());

        if ($this->isJsonEncodable($response)) {
            $body     = json_encode($response);
            $response = $this->responseFactory;
            $response->getBody()->write($body);
        }

        $response = $this->applyDefaultResponseHeaders($response);

        return $response;
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
     * @param NotFoundException $exception
     *
     * @return MiddlewareInterface
     */
    public function getNotFoundDecorator(
        NotFoundException $exception
    ): MiddlewareInterface {
        return $this->buildJsonResponseMiddleware($exception);
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
        return new class($this ->responseFactory ->createResponse(), $exception) implements MiddlewareInterface {
            protected $response;
            protected $exception;

            public function __construct(
                ResponseInterface $response,
                HttpException $exception
            ) {
                $this->response  = $response;
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
     * Get a middleware that will act as an exception handler.
     *
     * The middleware must wrap the rest of the middleware stack and catch any
     * thrown exceptions.
     *
     * @return MiddlewareInterface
     */
    public function getExceptionHandler(): MiddlewareInterface
    {
        return $this->getThrowableHandler();
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
        return new class($this->responseFactory->createResponse()) implements MiddlewareInterface {
            protected $response;

            public function __construct(ResponseInterface $response)
            {
                $this->response = $response;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                try {
                    return $requestHandler->handle($request);
                } catch (Throwable $exception) {
                    $response = $this->response;

                    if ($exception instanceof HttpException) {
                        return $exception->buildJsonResponse($response);
                    }

                    $response->getBody()->write(json_encode([
                        'code' => 500,
                        'status' => 'error',
                        'message' => $exception->getMessage(),
                        'data' => []
                    ]));

                    $response = $response
                        ->withAddedHeader('content-type', 'application/json');

                    $reason = strtok($exception->getMessage(), "\n");

                    return $response
                        ->withStatus(500, $reason);
                }
            }
        };
    }
}
