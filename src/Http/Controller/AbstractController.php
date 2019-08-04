<?php

namespace Wiring\Http\Controller;

use Exception;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Wiring\Interfaces\ConsoleInterface;
use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Interfaces\ControllerInterface;
use Wiring\Interfaces\ResponseAwareInterface;
use Wiring\Interfaces\ApplicationInterface;
use Wiring\Interfaces\AuthInterface;
use Wiring\Interfaces\CookieInterface;
use Wiring\Interfaces\ConfigInterface;
use Wiring\Interfaces\CsrfInterface;
use Wiring\Interfaces\DatabaseInterface;
use Wiring\Interfaces\FlashInterface;
use Wiring\Interfaces\HashInterface;
use Wiring\Strategy\AbstractStrategy;
use Wiring\Interfaces\RouterInterface;
use Wiring\Interfaces\SessionInterface;
use Wiring\Traits\ContainerAwareTrait;
use Wiring\Traits\ResponseAwareTrait;

abstract class AbstractController extends AbstractStrategy implements
    ContainerAwareInterface,
    ControllerInterface,
    ResponseAwareInterface
{
    use ContainerAwareTrait;
    use ResponseAwareTrait;

    /**
     * Get an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws Exception   Error while resolving the entry.
     *
     * @return mixed Entry.
     */
    public function get(string $id)
    {
        $container = $this->getContainer();

        if (!$container) {
            throw new Exception('Container not found');
        }

        return $container->get($id);
    }

    /**
     * Check if the container can return an entry for the given identifier.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        $container = $this->getContainer();
        return $container->has($id);
    }

    /**
     * Resolves an entry by its name.
     * If given a class name, it will return a new instance of that class.
     *
     * @param string $name  Entry name or a class name.
     * @param array $params Optional parameters to use to build the entry.
     *
     * @throws Exception    Error while resolving the entry.
     *
     * @return mixed
     */
    public function make(string $name, array $params = [])
    {
        $container = $this->getContainer();

        if (!method_exists($container, 'make')) {
            throw new Exception('Container method not found');
        }

        return $container->make($name, $params);
    }

    /**
     * Call the given function using the given parameters.
     *
     * Missing parameters will be resolved from the container.
     *
     * @param callable $callable Function to call.
     * @param array $parameters  Parameters to use.
     *
     * @throws Exception
     *
     * @return mixed Result of the function.
     */
    public function call(callable $callable, array $params = [])
    {
        $container = $this->getContainer();

        if (!method_exists($container, 'call')) {
            throw new Exception('Container method not found');
        }

        return $container->call($callable, $params);
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $name Entry name
     * @param mixed $value Value, use definition helpers to define objects.
     *
     * @throws Exception
     *
     * @return ContainerAwareInterface
     */
    public function set(string $name, $value): ContainerAwareInterface
    {
        $container = $this->getContainer();

        if (!method_exists($container, 'set')) {
            throw new Exception('Container method not found');
        }

        return $container->set($name, $value);
    }

    /**
     * Return application.
     *
     * @throws Exception
     *
     * @return AppFactory
     */
    public function app()
    {
        if (!$this->has(ApplicationInterface::class)) {
            throw new Exception('Application interface not set');
        }

        return $this->get(ApplicationInterface::class);
    }

    /**
     * Get authentication.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function auth()
    {
        if (!$this->has(AuthInterface::class)) {
            throw new Exception('Auth interface not set');
        }

        return $this->get(AuthInterface::class);
    }

    /**
     * Get on the client cookies.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function cookie()
    {
        if (!$this->has(CookieInterface::class)) {
            throw new Exception('Cookie interface not set');
        }

        return $this->get(AuthInterface::class);
    }

    /**
     * Get settings properties.
     *
     * @param $key
     * @throws Exception
     *
     * @return mixed
     */
    public function config($key)
    {
        if (!$this->has(ConfigInterface::class)) {
            throw new Exception('Config interface not set');
        }

        return $this->get(ConfigInterface::class)->get($key);
    }

    /**
     * Get console log.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function console()
    {
        if (!$this->has(ConsoleInterface::class)) {
            throw new Exception('Console interface not set');
        }

        return $this->get(ConsoleInterface::class);
    }

    /**
     * Get CSRF protection.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function csrf()
    {
        if (!$this->has(CsrfInterface::class)) {
            throw new Exception('CSRF interface not set');
        }

        return $this->get(CsrfInterface::class);
    }

    /**
     * Return database connection.
     *
     * @throws Exception
     *
     * @return DatabaseInterface
     */
    public function database(): DatabaseInterface
    {
        if (!$this->has(DatabaseInterface::class)) {
            throw new Exception('Database interface not set');
        }

        return $this->get(DatabaseInterface::class);
    }

    /**
     * Get flash messages.
     *
     * @param $type
     * @param $message
     * @throws Exception
     */
    public function flash($type, $message)
    {
        if (!$this->has(FlashInterface::class)) {
            throw new Exception('Flash interface not set');
        }

        return $this->get(FlashInterface::class)->addMessage($type, $message);
    }

    /**
     * Get hash object.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function hash()
    {
        if (!$this->has(HashInterface::class)) {
            throw new Exception('Hash interface not set');
        }

        return $this->get(HashInterface::class);
    }

    /**
     * Get message properties.
     *
     * @param $key
     * @return mixed
     */
    public function lang($key)
    {
        return $this->config("lang." . $key);
    }

    /**
     * Get logger object.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function logger()
    {
        if (!$this->has(LoggerInterface::class)) {
            throw new Exception('Logger interface not set');
        }

        return $this->get(LoggerInterface::class);
    }

    /**
     * Redirect response.
     *
     *
     * This method prepares the response object to return an
     * HTTP Redirect response to the client.
     *
     * @param ResponseInterface
     * @param string $url
     * @param int|null $status
     *
     * @return ResponseInterface $request
     */
    public function redirect(
        ResponseInterface $response,
        string $url,
        int $status = 307
    ) {
        $responseWithRedirect = $response->withHeader('Location', $url);

        if (!is_null($status)) {
            return $responseWithRedirect->withStatus($status);
        }

        return $responseWithRedirect;
    }

    /**
     * Output rendered template.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function router()
    {
        if (!$this->has(RouterInterface::class)) {
            throw new Exception('Router interface not set');
        }

        return $this->get(RouterInterface::class);
    }

    /**
     * Get session object.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function session()
    {
        if (!$this->has(SessionInterface::class)) {
            throw new Exception('Session interface not set');
        }

        return $this->get(SessionInterface::class);
    }
}
