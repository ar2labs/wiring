# API Reference

This reference documents every class, interface, trait, and declared method under `src/`. Method descriptions are written from the framework point of view. Interfaces marked as application responsibilities must be implemented by the consuming application or by another package.

## Core Application

### `Wiring\Application`

File: `src/Application.php`

Main application entry point. It extends `Wiring\Http\RequestHandler` and implements `ApplicationInterface`.

Methods:

* `run(): ResponseInterface` - Handles the current request through the normal middleware pipeline and returns the final response.
* `stop(): ResponseInterface` - Switches the handler into after-middleware mode and handles the current request through the after pipeline.

### `Wiring\Http\RequestHandler`

File: `src/Http/RequestHandler.php`

PSR-15 request handler that stores and executes the middleware pipeline.

Methods:

* `__construct(ContainerInterface $container, ServerRequestInterface $request, ResponseInterface $response, bool $errorHandler = false)` - Stores the container, request, response, and error-handler mode flag.
* `__destruct()` - Performs no request handling; applications should call `run()` and `stop()` explicitly.
* `handle(ServerRequestInterface $request): ResponseInterface` - Executes the next middleware registered for the active phase and returns the current response; exceptions are delegated to `errorHandler()`.
* `getMiddleware(string $key): ?MiddlewareInterface` - Returns middleware registered with the given key, or `null` when not found.
* `addMiddleware(MiddlewareInterface $middleware, ?string $key = null): RequestHandler` - Adds middleware to the pipeline with an optional key.
* `addRouterMiddleware(MiddlewareInterface $router): RequestHandler` - Adds router middleware using the `router` key.
* `addRequestHandler(MiddlewareInterface $RequestHandler): RequestHandler` - Adds request-handler middleware using the `RequestHandler` key.
* `addEmitterMiddleware(MiddlewareInterface $emitter): RequestHandler` - Adds emitter middleware as after middleware using the `emitter` key.
* `addAfterMiddleware(MiddlewareInterface $middleware, ?string $key): RequestHandler` - Adds middleware marked for the after phase.
* `removeMiddleware($key): RequestHandler` - Removes middleware by key when it exists.
* `isAfterMiddleware(): bool` - Returns whether the handler is currently in after-middleware mode.
* `setIsAfterMiddleware(bool $isAfterMiddleware = true): RequestHandler` - Sets after-middleware mode and returns the handler.
* `isFinished(): bool` - Returns whether the handler has finished processing.
* `findMiddleware(string $key): ?int` - Protected helper that returns the numeric position of middleware by key.
* `nextMiddlewarePosition(bool $after): ?int` - Protected helper that returns the next middleware position for the normal or after phase.
* `executeMiddleware(array $middlewareArray): void` - Protected helper that runs one middleware entry and stores its response.
* `errorHandler(Throwable $error, ServerRequestInterface $request, ResponseInterface $response): ResponseInterface` - Protected helper that calls the configured `ErrorHandlerInterface` service or writes a generic 500 response when no service is registered.

## Controllers

### `Wiring\Http\Controller\AbstractController`

File: `src/Http/Controller/AbstractController.php`

Base controller for routes that return PSR-7 responses. It provides container access, response access, database access, default response headers, exception decorators, and safe redirects.

Methods:

* `__construct(ContainerInterface $container, ResponseInterface $response)` - Stores the container and default response.
* `invokeRouteCallable(RouteInterface $route, ServerRequestInterface $request): ResponseInterface` - Resolves the route callable, invokes it with request and route variables, validates that it returns a response, and applies default headers.
* `addDefaultResponse(ResponseInterface $response): ResponseInterface` - Applies default response headers to an existing response.
* `redirect(ResponseInterface $response, string $url, ?int $status = 307): ResponseInterface` - Returns a response with a safe relative `Location` header and optional status code.
* `assertSafeRedirectUrl(string $url): void` - Protected validation helper that rejects empty URLs, surrounding whitespace, control characters, schemes, protocol-relative URLs, and backslashes.
* `getNotFoundDecorator(NotFoundException $exceptionNotFound): MiddlewareInterface` - Returns middleware that throws the provided not-found exception.
* `getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface` - Returns middleware that throws the provided method-not-allowed exception.
* `getExceptionHandler(): MiddlewareInterface` - Returns a throwable handler middleware.
* `getThrowableHandler(): MiddlewareInterface` - Returns middleware that catches, logs, and rethrows throwables from the downstream handler.
* `throwThrowableMiddleware(Throwable $error): MiddlewareInterface` - Protected helper that creates middleware which throws a fixed throwable.

### `Wiring\Http\Controller\AbstractViewController`

File: `src/Http/Controller/AbstractViewController.php`

Controller base class for template-rendered routes.

Methods:

* `view(): ViewStrategyInterface` - Retrieves `ViewStrategyInterface` from the container and validates the service type.

### `Wiring\Http\Controller\AbstractJsonController`

File: `src/Http/Controller/AbstractJsonController.php`

Controller base class for JSON routes. It adds `content-type: application/json` as a default response header.

Methods:

* `__construct(ContainerInterface $container, ResponseInterface $response)` - Adds the JSON default header and initializes the base controller.
* `invokeRouteCallable(RouteInterface $route, ServerRequestInterface $request): ResponseInterface` - Invokes the route callable and writes JSON when the result is an array or object; otherwise validates and returns a response.
* `isJsonEncodable($response): bool` - Protected helper that accepts arrays and objects except existing `ResponseInterface` instances.
* `json(): JsonStrategyInterface` - Retrieves `JsonStrategyInterface` from the container and validates the service type.
* `getNotFoundDecorator(NotFoundException $exceptionNotFound): MiddlewareInterface` - Returns middleware that builds a JSON response for a not-found exception.
* `getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface` - Returns middleware that builds a JSON response for a method-not-allowed exception.
* `buildJsonResponseMiddleware(HttpException $exception): MiddlewareInterface` - Protected helper that creates middleware for JSON HTTP exception responses.
* `getThrowableHandler(): MiddlewareInterface` - Returns middleware that catches throwables and writes a generic JSON error response.

### `Wiring\Http\Controller\AbstractJsonViewController`

File: `src/Http/Controller/AbstractJsonViewController.php`

Controller base class for routes that need both JSON and template strategies.

Methods:

* `json(): JsonStrategyInterface` - Retrieves and validates the JSON strategy service.
* `view(): ViewStrategyInterface` - Retrieves and validates the view strategy service.

### `Wiring\Http\Controller\AbstractRestfulController`

File: `src/Http/Controller/AbstractRestfulController.php`

REST-style JSON controller base class with default CRUD methods and response envelope helpers.

Methods:

* `index(ServerRequestInterface $request): ResponseInterface` - Returns the default not-implemented JSON response for listing resources.
* `create(ServerRequestInterface $request): ResponseInterface` - Returns the default not-implemented JSON response for creating a resource.
* `read(ServerRequestInterface $request, array $args): ResponseInterface` - Returns the default not-implemented JSON response for reading a resource.
* `update(ServerRequestInterface $request, array $args): ResponseInterface` - Returns the default not-implemented JSON response for updating a resource.
* `delete(ServerRequestInterface $request, array $args): ResponseInterface` - Returns the default not-implemented JSON response for deleting a resource.
* `info(string $message = 'Continue', int $status = 100, $data = [])` - Builds an informational response envelope.
* `success(string $message = 'OK', $data = [], int $status = 200)` - Builds a success response envelope.
* `error(string $message = 'Bad Request', int $status = 400, $data = [])` - Builds a client-error response envelope.
* `fail(string $message = 'Internal Server Error', int $status = 500, $data = [])` - Builds a server-error response envelope.
* `data(string $status, string $message = 'OK', $data = [], ?int $code = 200)` - Builds the shared response envelope with `code`, `status`, `message`, and `data` keys.
* `methodNotImplemented(): ResponseInterface` - Private helper that returns a 501 JSON response.

## Exceptions

### `Wiring\Http\Exception\HttpException`

File: `src/Http/Exception/HttpException.php`

Base exception for HTTP status-aware failures.

Methods:

* `__construct(int $status, string $message = '', ?Exception $previous = null, array $headers = [], int $code = 0)` - Stores status, message, previous exception, headers, and exception code.
* `getStatusCode(): int` - Returns the HTTP status code.
* `getHeaders(): array` - Returns headers attached to the exception.
* `getData(): array` - Returns structured error data built by response builders.
* `buildResponse(ResponseInterface $response): ResponseInterface` - Adds headers, derives response data, and returns the response with the exception status.
* `buildJsonResponse(ResponseInterface $response): ResponseInterface` - Adds JSON headers, writes an error JSON body when the stream is writable, and returns the response with the exception status.

### `Wiring\Http\Exception\BadRequestException`

File: `src/Http/Exception/BadRequestException.php`

HTTP 400 exception.

Methods:

* `__construct(string $message = 'Bad Request', ?Exception $previous = null, int $code = 0)` - Creates a 400 `HttpException`.

### `Wiring\Http\Exception\NotFoundException`

File: `src/Http/Exception/NotFoundException.php`

HTTP 404 exception.

Methods:

* `__construct(string $message = 'Not Found', ?Exception $previous = null, int $code = 0)` - Creates a 404 `HttpException`.

### `Wiring\Http\Exception\MethodNotAllowedException`

File: `src/Http/Exception/MethodNotAllowedException.php`

HTTP 405 exception with an `Allow` header.

Methods:

* `__construct(array $allowed = [], string $message = 'Method Not Allowed', ?Exception $previous = null, int $code = 0)` - Creates a 405 `HttpException` and stores allowed methods in the `Allow` header.

### `Wiring\Http\Exception\UnauthorizedException`

File: `src/Http/Exception/UnauthorizedException.php`

HTTP 401 exception.

Methods:

* `__construct(string $message = 'Unauthorized', ?Exception $previous = null, int $code = 0)` - Creates a 401 `HttpException`.

### `Wiring\Http\Exception\ErrorHandler`

File: `src/Http/Exception/ErrorHandler.php`

Converts throwables into structured HTML or JSON error arrays and optionally logs the error.

Methods:

* `__construct(ServerRequestInterface $request, ResponseInterface $response, Throwable $exception, ?LoggerInterface $logger = null, array $loggerContext = [], bool $debug = false)` - Stores request, response, exception, optional logger, logger context, and debug flag.
* `error(?string $message = null): array` - Builds an error array, chooses HTML or JSON from the request content type, updates debug mode from `Debug-Mode`, logs redacted details when a logger exists, and sets the HTTP response code.
* `getException(): ?Throwable` - Returns the wrapped exception.
* `isJson(): bool` - Returns whether the last `error()` result was built for JSON.
* `errorHtml(string $message): array` - Private helper that builds escaped HTML error data.
* `errorJson(string $message): array` - Private helper that builds JSON error data.
* `redactSensitiveText(string $value): string` - Private helper that redacts secret-like key-value pairs in log messages.
* `redactLoggerContext(array $context): array` - Private helper that recursively redacts secret-like logger context keys.
* `isSensitiveContextKey(string $key): bool` - Private helper that identifies sensitive logger context names.

## Middleware

### `Wiring\Http\Middleware\RouterMiddleware`

File: `src/Http/Middleware/RouterMiddleware.php`

PSR-15 middleware that dispatches the request through a router implementation. When a response factory is configured, Wiring HTTP exceptions thrown by the router are converted into PSR-7 responses.

Methods:

* `__construct(RouterInterface $router, ?ResponseFactoryInterface $responseFactory = null)` - Stores the router and optional response factory.
* `process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface` - Dispatches the request through the router, returns the router response, or builds a response for a thrown Wiring HTTP exception when a response factory is configured.
* `responseFactory(ResponseFactoryInterface $responseFactory): self` - Stores a response factory and returns the middleware.
* `getResponseFactory(): ?ResponseFactoryInterface` - Returns the configured response factory.

### `Wiring\Http\Middleware\EmitterMiddleware`

File: `src/Http/Middleware/EmitterMiddleware.php`

PSR-15 middleware that emits headers and body content for a response.

Methods:

* `__construct(?EmitterInterface $emitter = null)` - Stores an optional custom emitter.
* `process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface` - Handles the request, then emits the response when headers have not already been sent.
* `emit(ResponseInterface $response): ResponseInterface` - Emits headers and streams the response body unless the status code forbids a body.
* `emitHeader(ResponseInterface $response): int` - Private helper that validates and sends response headers and the status line.
* `assertHeaderLineSafe(string $value): void` - Private helper that rejects CR/LF header injection.

## Helpers

### `Wiring\Http\Helpers\Console`

File: `src/Http/Helpers/Console.php`

Stores JavaScript snippets in the session so applications can render browser console output.

Methods:

* `log($obj)` - Stores a `console.log()` call.
* `debug($debug)` - Stores a debug-style console log.
* `table($obj)` - Stores a `console.table()` call.
* `info($obj)` - Stores a `console.info()` call.
* `warn($obj)` - Stores a `console.warn()` call.
* `error($obj)` - Stores a `console.error()` call.
* `trace($obj)` - Stores a `console.trace()` call.
* `dir($obj)` - Stores a `console.dir()` call.
* `dirxml($obj)` - Stores a `console.dirxml()` call.
* `assert(...$args)` - Stores a `console.assert()` call using the first truthy argument.
* `clear()` - Clears stored console output from the session.
* `count(?string $name = 'even')` - Stores a `console.count()` call.
* `time($obj)` - Stores a `console.time()` call.
* `timeend($obj)` - Stores a `console.timeEnd()` call.
* `group($obj)` - Stores a `console.group()` call.
* `groupend($obj = null)` - Stores a `console.groupEnd()` call.
* `method(string $method, $obj)` - Private helper that builds and stores the JavaScript snippet.
* `encodeForJavaScript(mixed $value): string` - Private helper that JSON-encodes values with JavaScript-safe hex escaping.
* `store(string $output): void` - Private helper that appends output to the session log.

### `Wiring\Http\Helpers\Cookie`

File: `src/Http/Helpers/Cookie.php`

Static cookie helper with safer default attributes.

Methods:

* `get(string $name)` - Returns the cookie value when it is a string, array, or object; otherwise returns an empty string.
* `set(string $name, string $value = '', int $expiry = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = true): bool` - Sets a cookie with `SameSite=Lax`, `HttpOnly` by default, and HTTPS-aware `Secure` handling.
* `createCookieOptions(int $expiry, string $path, string $domain, bool $secure, bool $httponly): array` - Protected helper that creates the PHP `setcookie()` options array.
* `isHttpsRequest(): bool` - Protected helper that detects HTTPS from `$_SERVER` values.
* `has(string $name): bool` - Returns whether a cookie is set.
* `forget(string $name): bool` - Expires an existing cookie.

### `Wiring\Http\Helpers\Session`

File: `src/Http/Helpers/Session.php`

Static helper for direct `$_SESSION` access.

Methods:

* `get(string $key, ?string $default = '')` - Returns the stored session value or the default.
* `set(string $key, ?string $value)` - Stores and returns a session value.
* `has(string $key): bool` - Returns whether a session key exists.
* `forget(string $key)` - Removes a session key and returns whether removal happened.

### `Wiring\Http\Helpers\Loader`

File: `src/Http/Helpers/Loader.php`

Loads matching files from configured paths using `glob()`.

Methods:

* `__construct(array $filetypes = ['php'])` - Stores allowed file extensions.
* `addPath(string $path)` - Adds a directory path and returns the loader.
* `load(): array` - Returns all files matching the configured paths and file types.

### `Wiring\Http\Helpers\Mailer`

File: `src/Http/Helpers/Mailer.php`

Template-based mail sender built around `MailerInterface` and `ViewStrategyInterface`.

Methods:

* `__construct($mailer, ContainerInterface $container)` - Validates and stores the mailer implementation and container.
* `send(string $template, array $data, callable $callback): bool` - Renders a template, configures a `Message` through the callback, and sends the mailer.

### `Wiring\Http\Helpers\Mailtrap\Message`

File: `src/Http/Helpers/Mailtrap/Message.php`

Fluent message wrapper around a `MailerInterface` implementation.

Methods:

* `__construct($mailer)` - Validates and stores the mailer implementation.
* `to(string $address)` - Adds a recipient and returns the message.
* `subject(string $subject)` - Sets the message subject after UTF-8 to ISO-8859-1 conversion.
* `body(?string $body)` - Sets the message body after UTF-8 to ISO-8859-1 conversion.

### `Wiring\Http\Helpers\Info`

File: `src/Http/Helpers/Info.php`

Generates customized PHP information output.

Methods:

* `phpinfo()` - Captures `phpinfo()`, rewrites the heading with PHP and Wiring versions, and returns the generated content.

Security note: do not expose this helper in production routes unless access is strongly restricted.

## Strategies

### `Wiring\Strategy\AbstractStrategy`

File: `src/Strategy/AbstractStrategy.php`

Base strategy for default response header management.

Methods:

* `getDefaultResponseHeaders(): array` - Returns configured default response headers.
* `addDefaultResponseHeader(string $name, string $value): self` - Adds or replaces a default response header by lowercase name.
* `addDefaultResponseHeaders(array $headers): self` - Adds multiple default response headers.
* `applyDefaultResponseHeaders(ResponseInterface $response): ResponseInterface` - Protected helper that applies defaults without overwriting headers already present on the response.

### `Wiring\Strategy\JsonStrategy`

File: `src/Strategy/JsonStrategy.php`

Writes JSON response bodies.

Methods:

* `render($data, int $encodingOptions = 0): self` - Stores data to JSON-encode with the given options.
* `write($data): JsonStrategyInterface` - Stores raw data or pre-encoded JSON for the response body.
* `to(ResponseInterface $response, int $status = 200): ResponseInterface` - Writes the body, sets JSON content type, and returns the response with the status.
* `encode($data, int $encodingOptions): string` - Private helper that rejects resources and throws `InvalidArgumentException` for JSON encoding errors.

### `Wiring\Strategy\ViewStrategy`

File: `src/Strategy/ViewStrategy.php`

Renders template content or writes raw view data to a response.

Methods:

* `__construct($engine)` - Stores the template engine.
* `engine()` - Returns the template engine.
* `render($view, array $params = []): self` - Stores the view name and template parameters.
* `write(string $data): ViewStrategyInterface` - Stores raw response data.
* `to(ResponseInterface $response, int $status = 200): ResponseInterface` - Renders the template through `engine()->render()` or writes raw data, then returns the response with the status.

## Traits

### `Wiring\Traits\AuthAwareTrait`

Methods:

* `getAuth(): ?AuthInterface` - Returns the assigned auth service.
* `setAuth(AuthInterface $auth)` - Stores the auth service.
* `auth(): AuthInterface` - Retrieves and validates `AuthInterface` from the container.

### `Wiring\Traits\ConfigAwareTrait`

Methods:

* `getConfig(): ?ConfigInterface` - Returns the assigned config service.
* `setConfig(ConfigInterface $config)` - Stores the config service.
* `config(string $key = '')` - Retrieves config from the container and optionally returns a key value.
* `lang(string $key, $default = null)` - Reads a language config value by `lang.<key>`.

### `Wiring\Traits\ConsoleAwareTrait`

Methods:

* `getConsole(): ?ConsoleInterface` - Returns the assigned console service.
* `setConsole(ConsoleInterface $console)` - Stores the console service.
* `console(): ConsoleInterface` - Retrieves and validates `ConsoleInterface` from the container.

### `Wiring\Traits\ContainerAwareTrait`

Methods:

* `getContainer(): ?ContainerInterface` - Returns the assigned PSR-11 container.
* `setContainer(ContainerInterface $container)` - Stores the container.
* `get(string $id)` - Retrieves a service from the container or throws when no container is available.
* `has(string $id): bool` - Checks service availability in the container or throws when no container is available.

### `Wiring\Traits\CookieAwareTrait`

Methods:

* `getCookie(): ?CookieInterface` - Returns the assigned cookie service.
* `setCookie(CookieInterface $cookie)` - Stores the cookie service.
* `cookie(): CookieInterface` - Retrieves and validates `CookieInterface` from the container.

### `Wiring\Traits\DatabaseAwareTrait`

Methods:

* `database(string $connection = '')` - Retrieves `DatabaseInterface` from the container and optionally returns a named connection.

### `Wiring\Traits\FlashAwareTrait`

Methods:

* `getFlash(): ?FlashInterface` - Returns the assigned flash service.
* `setFlash(FlashInterface $flash)` - Stores the flash service.
* `flash(): FlashInterface` - Retrieves and validates `FlashInterface` from the container.

### `Wiring\Traits\HashAwareTrait`

Methods:

* `getHash(): ?HashInterface` - Returns the assigned hash service.
* `setHash(HashInterface $hash)` - Stores the hash service.
* `hash(): ?HashInterface` - Retrieves and validates `HashInterface` from the container.

### `Wiring\Traits\InputAwareTrait`

Methods:

* `input(ServerRequestInterface $request, bool $isArray = false)` - Parses request body content as form data, URL-encoded data, JSON, XML, or raw body content.
* `query(ServerRequestInterface $request, bool $isArray = false)` - Returns query parameters as an array or object.

### `Wiring\Traits\LoggerAwareTrait`

Methods:

* `getLogger(): ?LoggerInterface` - Returns the assigned PSR-3 logger.
* `setLogger(LoggerInterface $logger)` - Stores the logger.
* `logger(): LoggerInterface` - Retrieves and validates `LoggerInterface` from the container.

### `Wiring\Traits\ResponseAwareTrait`

Methods:

* `getResponse(): ResponseInterface` - Returns the assigned response.
* `setResponse(ResponseInterface $response)` - Stores the response.

### `Wiring\Traits\SessionAwareTrait`

Methods:

* `getSession(): ?SessionInterface` - Returns the assigned session service.
* `setSession(SessionInterface $session)` - Stores the session service.
* `session(): SessionInterface` - Retrieves and validates `SessionInterface` from the container.

### `Wiring\Traits\ValidatorAwareTrait`

Methods:

* `getValidator(): ?ValidatorInterface` - Returns the assigned validator service.
* `setValidator(ValidatorInterface $validator)` - Stores the validator service.
* `validator(): ValidatorInterface` - Retrieves and validates `ValidatorInterface` from the container.

## Interfaces

### `Wiring\Interfaces\ApplicationInterface`

* `run(): ResponseInterface` - Runs the application.
* `stop(): ResponseInterface` - Stops or finalizes the application lifecycle.

### `Wiring\Interfaces\AuthInterface`

* `check(): bool` - Returns whether a user is authenticated.
* `user(): ?object` - Returns the authenticated user object or `null`.

### `Wiring\Interfaces\ConfigInterface`

* `__construct(array $path)` - Creates a config service with one or more paths.
* `load($path): ConfigInterface` - Loads configuration from a path.
* `get(string $key, $default = null)` - Returns a config value or default.
* `set(string $key, $value): void` - Stores a config value.
* `has(string $key): bool` - Checks whether a config key exists.
* `all(): ?array` - Returns all config values.
* `offsetGet(string $offset)` - Returns an ArrayAccess value.
* `offsetExists(string $offset): bool` - Checks ArrayAccess key existence.
* `offsetSet(string $offset, $value): void` - Sets an ArrayAccess value.
* `offsetUnset(string $offset): void` - Removes an ArrayAccess value.
* `current()` - Returns the current iterator value.
* `key()` - Returns the current iterator key.
* `next()` - Advances the iterator.
* `rewind()` - Rewinds the iterator.
* `valid(): bool` - Checks whether the iterator position is valid.

### `Wiring\Interfaces\ConsoleInterface`

* `log($obj)` - Logs a value to the browser console integration.

### `Wiring\Interfaces\ContainerAwareInterface`

* `getContainer(): ?ContainerInterface` - Returns the assigned container.
* `setContainer(ContainerInterface $container)` - Stores the container.

### `Wiring\Interfaces\ControllerInterface`

* `get(string $id)` - Retrieves a container entry.
* `has(string $id): bool` - Checks whether a container entry exists.

### `Wiring\Interfaces\CookieInterface`

* `get(string $name)` - Returns a cookie value.
* `set(string $name, string $value = '', int $expiry = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = true)` - Sets a cookie.
* `has(string $name): bool` - Checks whether a cookie exists.
* `forget(string $name): bool` - Deletes a cookie.

### `Wiring\Interfaces\CsrfInterface`

* `getTokenNameKey(): ?string` - Returns the request key used for CSRF token names.
* `getTokenValueKey(): ?string` - Returns the request key used for CSRF token values.
* `validateStorage($prefix, $storage)` - Validates or initializes token storage.
* `generateToken(): array` - Generates a CSRF token array.
* `generateNewToken(ServerRequestInterface $request): ServerRequestInterface` - Generates a token and attaches it to the request.
* `validateToken(string $name, string $value): bool` - Validates a submitted CSRF token.

### `Wiring\Interfaces\DatabaseInterface`

* `connection(string $dbname = 'default')` - Returns a database connection by name.

### `Wiring\Interfaces\EmitterInterface`

* `emit(ResponseInterface $response): ResponseInterface` - Emits a response and returns it.

### `Wiring\Interfaces\ErrorHandlerInterface`

* `error(?string $message = null): array` - Builds structured error data.
* `getException()` - Returns the wrapped exception.
* `isJson(): bool` - Returns whether the current error output is JSON.

### `Wiring\Interfaces\FlashInterface`

* `addMessage(string $key, $message)` - Adds a flash message for the next request.
* `addMessageNow(string $key, string $message)` - Adds a flash message for the current request.
* `hasMessage(string $key): bool` - Checks whether a flash message exists.
* `getMessage(string $key): ?string` - Returns a flash message.
* `getMessages(): array` - Returns all flash messages for the current request.
* `clearMessage(string $key)` - Clears one flash message.
* `clearMessages()` - Clears all flash messages.

### `Wiring\Interfaces\HashInterface`

* `password(string $password): bool` - Creates or stores a password representation in the implementation.
* `verifyPassword(string $givenPassword, string $knownPassword): bool` - Verifies a given password against a known password value.
* `generate(string $characters, int $length = 64): string` - Generates a random string from allowed characters.
* `hash(string $input): string` - Returns a SHA-256 hash string.
* `verifyHash($knownHash, $givenHash): bool` - Verifies two hash values.

### `Wiring\Interfaces\HttpExceptionInterface`

* `getStatusCode(): int` - Returns the HTTP status code.
* `getHeaders(): array` - Returns headers attached to the exception.
* `buildJsonResponse(ResponseInterface $response): ResponseInterface` - Builds a JSON response from the exception.

### `Wiring\Interfaces\JsonStrategyInterface`

* `render($data, int $encodingOptions = 0)` - Stores data for JSON encoding.
* `write($data)` - Stores raw or pre-encoded JSON data.
* `to(ResponseInterface $response, int $status = 200): ResponseInterface` - Writes data to a response and applies the status.

### `Wiring\Interfaces\MailerInterface`

Properties:

* `public string $Subject { get; set; }` - Message subject.
* `public string $Body { get; set; }` - Message body.

Methods:

* `addAddress(string $email): void` - Adds a recipient email address.
* `send(): bool` - Sends the message.

### `Wiring\Interfaces\MessageInterface`

* `to(string $address)` - Adds a recipient and returns the message.
* `subject(string $subject)` - Sets the subject and returns the message.
* `body(?string $body)` - Sets the body and returns the message.

### `Wiring\Interfaces\MiddlewareInvokerInterface`

* `__invoke(ServerRequestInterface $request, ResponseInterface $response, ?callable $next): ResponseInterface` - Invokes middleware-like behavior with request, response, and optional next callable.

### `Wiring\Interfaces\QueryInterface`

* `set(string $query): void` - Stores a query string.

### `Wiring\Interfaces\ResponseAwareInterface`

* `getResponse(): ?ResponseInterface` - Returns the assigned response.
* `setResponse(ResponseInterface $response)` - Stores the response.

### `Wiring\Interfaces\RestfulControllerInterface`

* `index(ServerRequestInterface $request): ResponseInterface` - Lists resources.
* `create(ServerRequestInterface $request)` - Creates a resource.
* `read(ServerRequestInterface $request, array $args): ResponseInterface` - Reads a resource.
* `update(ServerRequestInterface $request, array $args): ResponseInterface` - Updates a resource.
* `delete(ServerRequestInterface $request, array $args): ResponseInterface` - Deletes a resource.

### `Wiring\Interfaces\RouteInterface`

* `getCallable(?ContainerInterface $container = null): callable` - Returns the route callable.
* `getVars(): array` - Returns route variables.
* `setVars(array $vars): self` - Stores route variables and returns the route.

### `Wiring\Interfaces\RouterInterface`

* `dispatch(ServerRequestInterface $request): ResponseInterface` - Dispatches a request and returns a response.

### `Wiring\Interfaces\SessionInterface`

* `get(string $key, ?string $default = '')` - Returns a session value or default.
* `set(string $key, ?string $value)` - Stores a session value.
* `has(string $key): bool` - Checks whether a session key exists.
* `forget(string $key)` - Removes a session key.

### `Wiring\Interfaces\StrategyAwareInterface`

* `getStrategy(): ?StrategyInterface` - Returns the assigned strategy.
* `setStrategy(StrategyInterface $strategy): StrategyAwareInterface` - Stores a strategy and returns the object.

### `Wiring\Interfaces\StrategyInterface`

* `invokeRouteCallable(RouteInterface $route, ServerRequestInterface $request): ResponseInterface` - Invokes a route callable according to the strategy.
* `getNotFoundDecorator(NotFoundException $exception): MiddlewareInterface` - Builds not-found decorator middleware.
* `getMethodNotAllowedDecorator(MethodNotAllowedException $exception): MiddlewareInterface` - Builds method-not-allowed decorator middleware.
* `getExceptionHandler(): MiddlewareInterface` - Builds exception handler middleware.
* `getThrowableHandler(): MiddlewareInterface` - Builds throwable handler middleware.

### `Wiring\Interfaces\ValidatorInterface`

* `validate(array $input, $rules = [])` - Validates input against rules and returns the validator.
* `passes()` - Returns whether validation passed.
* `fails()` - Returns whether validation failed.
* `errors()` - Returns validation errors.
* `addRuleMessage($rule, $message)` - Adds a custom rule message.
* `addRuleMessages(array $messages)` - Adds multiple custom rule messages.
* `addFieldMessage($field, $rule, $message)` - Adds a custom field-specific rule message.
* `addFieldMessages(array $messages)` - Adds multiple custom field messages.
* `addRule($name, Closure $callback)` - Registers a custom validation rule.

### `Wiring\Interfaces\ViewStrategyInterface`

* `__construct($engine)` - Creates a view strategy with a rendering engine.
* `engine()` - Returns the rendering engine.
* `render($view, array $params = [])` - Stores a view name and parameters.
* `write(string $data): ViewStrategyInterface` - Stores raw response data.
* `to(ResponseInterface $response, int $status = 200): ResponseInterface` - Writes rendered or raw data to a response.