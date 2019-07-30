<?php

namespace Wiring\Http\Controller;

use Exception;
use Psr\Http\Message\ResponseInterface;
use Wiring\Interfaces\{
    ConsoleInterface,
    ContainerAwareInterface,
    ControllerInterface,
    ResponseAwareInterface,
    ApplicationInterface,
    AuthInterface,
    CookieInterface,
    ConfigInterface,
    CsrfInterface,
    DatabaseInterface,
    FlashInterface,
    HashInterface
};
use Wiring\Strategy\{AbstractStrategy, ContainerAwareTrait, ResponseAwareTrait};
use Psr\Log\LoggerInterface;
use Wiring\Interfaces\RouterInterface;
use Wiring\Interfaces\SessionInterface;

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
    public function get($id)
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
     * @return boolean
     */
    public function has($id)
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
     *                      Use this to force specific parameters to
     *                      specific values.
     *                      Parameters not set in this array will be
     *                      automatically resolved.
     *
     * @throws Exception   Error while resolving the entry.
     *
     * @return mixed
     */
    public function make($name, array $params = [])
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
     *                           Can be indexed by the parameter names
     *                           or not indexed (same order as the parameters).
     *                           The array can also contain DI definitions,
     *                           e.g. DI\get().
     *
     * @throws Exception
     *
     * @return mixed Result of the function.
     */
    public function call($callable, array $params = [])
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
     */
    public function set($name, $value)
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
    public function database()
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
     * Redirect.
     *
     * Note: This method is not part of the PSR-7 standard.
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
        return $this->withRedirect($response, $url, $status);
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

    /**
     * Response with redirect.
     *
     * @param $response ResponseInterface
     * @param string $url
     * @param int|null $status
     *
     * @return ResponseInterface
     */
    protected function withRedirect(
        ResponseInterface $response,
        string $url,
        $status = 307
    ) {
        $responseWithRedirect = $response->withHeader('Location', $url);

        if (is_null($status) && $response->getStatusCode() === 200) {
            $status = 307;
        }

        if (!is_null($status)) {
            return $responseWithRedirect->withStatus($status);
        }

        return $responseWithRedirect;
    }
}
