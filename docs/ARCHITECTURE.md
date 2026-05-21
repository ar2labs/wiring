# Architecture

## Design Goals

Wiring is a minimal framework core built around PHP-FIG standards. The package aims to keep application behavior explicit, testable, and easy to extend through PSR-compatible interfaces.

The framework is not a full-stack application runtime. It provides the structure that lets an application combine a container, PSR-7 messages, PSR-15 middleware, route dispatching, response rendering, helper services, and error handling.

## Request Lifecycle

1. An application creates `Wiring\Application` with a PSR-11 container, PSR-7 server request, and PSR-7 response.
2. Middleware is registered on the inherited `RequestHandler` pipeline.
3. `Application::run()` calls `RequestHandler::handle($request)`.
4. `RequestHandler` executes the next middleware in registration order.
5. Middleware returns a `ResponseInterface` directly or calls `$handler->handle($request)` to continue.
6. Router middleware dispatches the request to a route implementation.
7. Controller strategies execute route callables and convert results into PSR-7 responses.
8. Emitter middleware sends headers and body output when configured.
9. Exceptions are converted by the configured error handler service or by controller throwable handlers.

## Middleware Pipeline

`RequestHandler` stores middleware entries with three values: an optional key, a middleware instance, and an `after` flag. Middleware can be registered with specific helper methods:

* `addMiddleware()` for general middleware.
* `addRouterMiddleware()` for router dispatch middleware.
* `addRequestHandler()` for request handler middleware.
* `addEmitterMiddleware()` for response emission middleware.
* `addAfterMiddleware()` for after-phase middleware.

Middleware can be looked up by key, removed by key, and executed through the PSR-15 `handle()` method.

## Controllers And Strategies

Controllers are abstract because this package does not define application routes. A route implementation supplies a callable through `RouteInterface::getCallable()` and route variables through `RouteInterface::getVars()`.

`AbstractController` expects route callables to return `ResponseInterface`. `AbstractJsonController` also accepts JSON-encodable arrays and objects and writes them to the response body.

Response strategies separate rendering from controllers:

* `JsonStrategy` encodes data and writes a JSON response.
* `ViewStrategy` delegates template rendering to an injected engine.
* `AbstractStrategy` stores default response headers and applies them without overwriting existing headers.

## Error Handling

HTTP exceptions extend `HttpException` and can build HTML or JSON responses. `ErrorHandler` wraps a `Throwable`, a request, and a response, then returns structured error data.

Production error output should stay generic. Debug details are available only when debug mode is enabled. Logger messages and context values are redacted for common secret-bearing names.

## Security Boundaries

The framework includes these core protections:

* Controller redirects accept only safe relative URLs.
* XML body parsing disables network access.
* Error output uses explicit HTML escaping.
* Production error output avoids raw exception details by default.
* Logger context redacts common secret keys.
* Console helper output is encoded for JavaScript contexts.
* Cookie defaults include `HttpOnly` and `SameSite=Lax`.
* Header emission rejects CR/LF line breaks.

The consuming application remains responsible for:

* Route-level input validation.
* Context-aware template escaping.
* Authentication and authorization.
* CSRF enforcement for state-changing requests.
* Session lifecycle and session ID regeneration.
* Prepared statements for database queries.
* File upload validation and path traversal prevention.
* SSRF controls for outbound HTTP clients.
* Secret management and production deployment configuration.

## Dependency Injection

The framework expects services to be retrieved from a PSR-11 container. Aware traits provide convenience accessors for common services and validate returned service types when possible.

Common service interfaces include `AuthInterface`, `ConfigInterface`, `CookieInterface`, `DatabaseInterface`, `FlashInterface`, `HashInterface`, `LoggerInterface`, `SessionInterface`, and `ValidatorInterface`.