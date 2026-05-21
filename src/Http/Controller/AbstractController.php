<?php

declare(strict_types=1);

namespace Wiring\Http\Controller;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use UnexpectedValueException;
use Wiring\Http\Exception\MethodNotAllowedException;
use Wiring\Http\Exception\NotFoundException;
use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Interfaces\ControllerInterface;
use Wiring\Interfaces\ResponseAwareInterface;
use Wiring\Interfaces\RouteInterface;
use Wiring\Strategy\AbstractStrategy;
use Wiring\Traits\ContainerAwareTrait;
use Wiring\Traits\DatabaseAwareTrait;
use Wiring\Traits\ResponseAwareTrait;

abstract class AbstractController extends AbstractStrategy implements
    ContainerAwareInterface,
    ControllerInterface,
    ResponseAwareInterface
{
    // NOTE: Use Aware Traits for get instance of the other components.
    use ContainerAwareTrait;
    use ResponseAwareTrait;
    use DatabaseAwareTrait;

    private const DEFAULT_STATUS_CODE_REDIRECT = 307;

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
        $this->setContainer($container);
        $this->setResponse($response);
    }

    /**
     * @param RouteInterface $route
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

        if (!$response instanceof ResponseInterface) {
            throw new UnexpectedValueException('Route callable must return a response.');
        }

        return $this->applyDefaultResponseHeaders($response);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function addDefaultResponse(
        ResponseInterface $response
    ): ResponseInterface {
        return $this->applyDefaultResponseHeaders($response);
    }

    /**
     * Redirect response.
     *
     * This method prepares the response object to return an
     * HTTP Redirect response to the client.
     *
     * @param ResponseInterface $response
     * @param string $url
     * @param int|null $status
     *
     * @return ResponseInterface $request
     */
    public function redirect(
        ResponseInterface $response,
        string $url,
        ?int $status = self::DEFAULT_STATUS_CODE_REDIRECT
    ): ResponseInterface {
        $this->assertSafeRedirectUrl($url);

        $responseWithRedirect = $response->withHeader('Location', $url);

        if (!is_null($status)) {
            return $responseWithRedirect->withStatus($status);
        }

        return $responseWithRedirect;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function assertSafeRedirectUrl(string $url): void
    {
        $decodedUrl = rawurldecode($url);

        if ($url === '' || trim($url) !== $url) {
            throw new InvalidArgumentException('Redirect URL must be a non-empty relative URL.');
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $url . $decodedUrl) === 1) {
            throw new InvalidArgumentException('Redirect URL must not contain control characters.');
        }

        if (str_contains($url, '\\') || str_starts_with($url, '//')) {
            throw new InvalidArgumentException('Redirect URL must not contain a scheme or host.');
        }

        if (preg_match('/^[A-Za-z][A-Za-z0-9+.-]*:/', $url) === 1) {
            throw new InvalidArgumentException('Redirect URL must not contain a scheme or host.');
        }
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
        return $this->throwThrowableMiddleware($exceptionNotFound);
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
        return $this->throwThrowableMiddleware($exception);
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
     * Get a middleware that acts as a throwable handler, it should wrap the
     * rest of the middleware stack and catch any throwables.
     *
     * @return MiddlewareInterface
     */
    public function getThrowableHandler(): MiddlewareInterface
    {
        return new class () implements MiddlewareInterface {
            /**
             * {@inheritdoc}
             *
             * @throws Throwable
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                try {
                    return $requestHandler->handle($request);
                } catch (Throwable $e) {
                    error_log($e->getMessage());

                    throw $e;
                }
            }
        };
    }

    /**
     * Return a middleware that simply throws an error.
     *
     * @param Throwable $error
     *
     * @return MiddlewareInterface
     */
    protected function throwThrowableMiddleware(
        Throwable $error
    ): MiddlewareInterface {
        return new class ($error) implements MiddlewareInterface {
            /** @var Throwable $error */
            protected $error;

            public function __construct(Throwable $error)
            {
                $this->error = $error;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $requestHandler
            ): ResponseInterface {
                throw $this->error;
            }
        };
    }
}
