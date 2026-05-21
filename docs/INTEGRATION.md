# Integration Guide

Wiring is a framework core, so application conveniences should live in the consuming project or in a starter package. This guide describes the recommended runtime shape for a small application without adding concrete router, container, PSR-7, or template dependencies to this package.

## Recommended Runtime Pieces

A production application should provide these concrete services:

* PSR-11 container.
* PSR-7 server request and response implementation.
* PSR-17 response factory.
* `Wiring\Interfaces\RouterInterface` implementation.
* Optional `Wiring\Interfaces\ErrorHandlerInterface` callable service.
* `Wiring\Interfaces\JsonStrategyInterface` service for JSON controllers.
* Optional `Wiring\Interfaces\ViewStrategyInterface` service for template controllers.
* Optional application middleware for trusted proxies, sessions, authentication, authorization, CSRF, and request validation.

## Minimal Bootstrap Shape

```php
<?php

declare(strict_types=1);

use Wiring\Application;
use Wiring\Http\Middleware\EmitterMiddleware;
use Wiring\Http\Middleware\RouterMiddleware;

$request = $serverRequestCreator->fromGlobals();
$response = $responseFactory->createResponse();

$app = new Application($container, $request, $response);
$app->addMiddleware($trustedProxyMiddleware, 'trusted-proxy');
$app->addMiddleware($sessionMiddleware, 'session');
$app->addMiddleware($csrfMiddleware, 'csrf');
$app->addRouterMiddleware(new RouterMiddleware($router, $responseFactory));
$app->addEmitterMiddleware(new EmitterMiddleware());

$app->run();
$app->stop();
```

`run()` executes normal middleware and route dispatch. `stop()` executes after middleware such as emitters. Applications should call both explicitly instead of relying on object destruction.

## Router Adapter Contract

The router service should implement `RouterInterface::dispatch(ServerRequestInterface $request): ResponseInterface`. It can either return a response directly or throw a Wiring HTTP exception such as `NotFoundException` or `MethodNotAllowedException`.

When `RouterMiddleware` receives a PSR-17 response factory, those Wiring HTTP exceptions are converted into PSR-7 responses. Without a response factory, the exception is rethrown for an upstream error handler.

## Container Service Checklist

At minimum, a JSON API starter should register:

* `RouterInterface` as the concrete application router.
* `JsonStrategyInterface` as `Wiring\Strategy\JsonStrategy`.
* `ErrorHandlerInterface` as an application callable when custom error output is needed.
* PSR logger service when application error handlers log failures.

A template starter can additionally register:

* `ViewStrategyInterface` as `Wiring\Strategy\ViewStrategy` configured with the selected template engine.
* Template engine globals, escaping policy, and view paths.

## Starter Package Checklist

A separate starter project should include:

* `public/index.php` with the bootstrap shape above.
* Concrete Composer dependencies for PSR-7, PSR-17, container, routing, and emitting.
* Environment loading and production/debug configuration.
* Example JSON and HTML controllers.
* Example route definitions and 404/405 handling.
* Security middleware examples for sessions, CSRF, authentication, authorization, trusted proxies, and body-size limits.
* Test fixtures that exercise one JSON route, one HTML route, and one error route.

Keeping these choices outside `ar2labs/wiring` preserves the core package as a small interoperability layer while giving applications a clear path to a complete runtime.