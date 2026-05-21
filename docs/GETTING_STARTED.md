# Getting Started

## Requirements

Wiring requires:

* PHP 8.5 or newer within the `^8.5` Composer constraint.
* `ext-json`.
* `ext-mbstring`.
* Composer for dependency installation.

Development checks also use PHPUnit, PHPStan, PHP-CS-Fixer, DOM/XML-related extensions, and Xdebug for Clover coverage generation.

## Installation

Install the package with Composer:

```bash
composer require ar2labs/wiring
```

The package autoloads the `Wiring\` namespace from `src/`.

## Core Building Blocks

Wiring is composed of five main parts:

* `Application` and `RequestHandler` execute the PSR-15 middleware pipeline.
* Controllers execute route callables and convert return values into PSR-7 responses.
* Strategies render JSON or template responses.
* Middleware adapts routing and response emission into the request lifecycle.
* Interfaces and traits define integration points for containers, sessions, cookies, validators, databases, mailers, authentication, logging, and other services.

## Minimal Runtime Shape

The framework expects a PSR-11 container, a PSR-7 server request, and a PSR-7 response object:

```php
use Wiring\Application;

$app = new Application($container, $request, $response);
$app->addMiddleware($middleware, 'name');

$response = $app->run();
```

The concrete container, request, response, router, emitter, and middleware implementations are supplied by the application.

## Middleware

Add middleware in the order it should run:

```php
$app->addMiddleware($middleware, 'auth');
$app->addRouterMiddleware($routerMiddleware);
$app->addEmitterMiddleware($emitterMiddleware);
```

Each middleware must implement `Psr\Http\Server\MiddlewareInterface`. Middleware continues the pipeline by calling `$handler->handle($request)` and returns a `Psr\Http\Message\ResponseInterface`.

Router middleware can be configured with a PSR-17 response factory. When the router throws a Wiring HTTP exception, the middleware can convert it into a 404, 405, or other HTTP response:

```php
$routerMiddleware = new RouterMiddleware($router, $responseFactory);
$app->addRouterMiddleware($routerMiddleware);
```

## Controllers

Controller base classes provide common behavior:

* `AbstractController` for standard PSR-7 responses.
* `AbstractJsonController` for JSON APIs.
* `AbstractViewController` for template responses.
* `AbstractJsonViewController` for controllers that need both JSON and templates.
* `AbstractRestfulController` for CRUD-style JSON resources.

Route callables used with `AbstractController` must return a `ResponseInterface`. Route callables used with `AbstractJsonController` may return a `ResponseInterface` or a JSON-encodable array/object.

## Response Strategies

Use `JsonStrategy` to render JSON responses:

```php
$response = $json->render(['status' => 'success'])->to($response, 200);
```

Use `ViewStrategy` with any engine that exposes a callable `render($view, $params)` method:

```php
$response = $view->render('home', ['name' => 'Wiring'])->to($response);
```

## Quality Gates

Before releasing changes, run:

```bash
composer validate --strict
composer install
composer audit --locked --abandoned=fail
composer check-platform-reqs
vendor/bin/php-cs-fixer fix --config=php_cs.dist --dry-run --diff --no-interaction
vendor/bin/phpstan analyse --configuration phpstan.neon --no-progress --ansi
vendor/bin/phpunit --configuration phpunit.xml.dist --colors=always
vendor/bin/phpunit --configuration phpunit.xml.dist --coverage-clover build/logs/clover.xml --colors=always
```