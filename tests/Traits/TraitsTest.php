<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use BadMethodCallException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Wiring\Interfaces\AuthInterface;
use Wiring\Interfaces\ConfigInterface;
use Wiring\Interfaces\ConsoleInterface;
use Wiring\Interfaces\CookieInterface;
use Wiring\Interfaces\DatabaseInterface;
use Wiring\Interfaces\FlashInterface;
use Wiring\Interfaces\HashInterface;
use Wiring\Interfaces\SessionInterface;
use Wiring\Interfaces\ValidatorInterface;

final class TraitsTest extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testAuthAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleAuthAware = new SimpleAuthAware();
        $auth = $this->createAuthMock();

        $container->method('has')
            ->with(AuthInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(AuthInterface::class)
            ->willReturn($auth);

        $simpleAuthAware->setContainer($container);
        $simpleAuthAware->setAuth($auth);

        $this->assertInstanceOf(AuthInterface::class,
            $simpleAuthAware->getAuth());
        $this->assertInstanceOf(AuthInterface::class,
            $simpleAuthAware->auth());

        // States that auth interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(AuthInterface::class)
            ->willReturn(false);

        $simpleAuthAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleAuthAware->auth());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Auth interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testConfigAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleConfigAware = new SimpleConfigAware();
        $config = $this->createConfigMock();

        $container->method('has')
            ->with(ConfigInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(ConfigInterface::class)
            ->willReturn($config);

        $simpleConfigAware->setContainer($container);
        $simpleConfigAware->setConfig($config);

        $this->assertInstanceOf(ConfigInterface::class,
            $simpleConfigAware->getConfig());
        $this->assertInstanceOf(ConfigInterface::class,
            $simpleConfigAware->config());
        $this->assertNull($simpleConfigAware->lang('test'));

        // States that config interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(ConfigInterface::class)
            ->willReturn(false);

        $simpleConfigAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleConfigAware->config());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Config interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testConsoleAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleConsoleAware = new SimpleConsoleAware();
        $console = $this->createConsoleMock();

        $container->method('has')
            ->with(ConsoleInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(ConsoleInterface::class)
            ->willReturn($console);

        $simpleConsoleAware->setContainer($container);
        $simpleConsoleAware->setConsole($console);

        $this->assertInstanceOf(ConsoleInterface::class,
            $simpleConsoleAware->getConsole());
        $this->assertInstanceOf(ConsoleInterface::class,
            $simpleConsoleAware->console());

        // States that console interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(ConsoleInterface::class)
            ->willReturn(false);

        $simpleConsoleAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleConsoleAware->console());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Console interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testContainerAwareTrait()
    {
        $simpleContainerAware = new SimpleContainerAware();
        $simpleContainerAware->getContainer();

        try {
            $this->assertInstanceOf(BadMethodCallException::class,
                $simpleContainerAware->get('test'));
        } catch (Exception $e) {
            $this->assertInstanceOf(BadMethodCallException::class, $e);
            $this->assertEquals('Method get does not exist.', $e->getMessage());
        }

        try {
            $this->assertInstanceOf(BadMethodCallException::class,
                $simpleContainerAware->has('test'));
        } catch (Exception $e) {
            $this->assertInstanceOf(BadMethodCallException::class, $e);
            $this->assertEquals('Method has does not exist.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testCookieAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleCookieAware = new SimpleCookieAware();
        $cookie = $this->createCookieMock();

        $container->method('has')
            ->with(CookieInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(CookieInterface::class)
            ->willReturn($cookie);

        $simpleCookieAware->setContainer($container);
        $simpleCookieAware->setCookie($cookie);

        $this->assertInstanceOf(CookieInterface::class,
            $simpleCookieAware->getCookie());
        $this->assertInstanceOf(CookieInterface::class,
            $simpleCookieAware->cookie());

        // States that cookie interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(CookieInterface::class)
            ->willReturn(false);

        $simpleCookieAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleCookieAware->cookie());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Cookie interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testDatabaseAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleDatabaseAware = new SimpleDatabaseAware();
        $database = $this->createDatabaseMock();

        $container->method('has')
            ->with(DatabaseInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(DatabaseInterface::class)
            ->willReturn($database);

        $simpleDatabaseAware->setContainer($container);

        $this->assertInstanceOf(DatabaseInterface::class,
            $simpleDatabaseAware->database());

        $database = $this->createDatabaseMockBuilder();
        $database->method('database')
            ->willReturn(DatabaseInterface::class);

        $container = $this->createContainerMock();
        $container->method('has')
            ->with(DatabaseInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(DatabaseInterface::class)
            ->willReturn($database);

        $simpleDatabaseAware->setContainer($container);

        $database->method('connection')
            ->with('default')
            ->willReturnSelf();

        $container->method('get')
            ->with(DatabaseInterface::class)
            ->willReturn($database);

        $this->assertInstanceOf(DatabaseInterface::class,
            $simpleDatabaseAware->database('default'));

        // States that database interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(DatabaseInterface::class)
            ->willReturn(false);

        $simpleDatabaseAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleDatabaseAware->database());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Database interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testFlashAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleFlashAware = new SimpleFlashAware();
        $flash = $this->createFlashMock();

        $container->method('has')
            ->with(FlashInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(FlashInterface::class)
            ->willReturn($flash);

        $simpleFlashAware->setContainer($container);
        $simpleFlashAware->setFlash($flash);

        $this->assertInstanceOf(FlashInterface::class,
            $simpleFlashAware->getFlash());
        $this->assertInstanceOf(FlashInterface::class,
            $simpleFlashAware->flash());

        // States that flash interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(FlashInterface::class)
            ->willReturn(false);

        $simpleFlashAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleFlashAware->flash());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Flash interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testHashAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleHashAware = new SimpleHashAware();
        $hash = $this->createHashMock();

        $container->method('has')
            ->with(HashInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(HashInterface::class)
            ->willReturn($hash);

        $simpleHashAware->setContainer($container);
        $simpleHashAware->setHash($hash);

        $this->assertInstanceOf(HashInterface::class,
            $simpleHashAware->getHash());
        $this->assertInstanceOf(HashInterface::class,
            $simpleHashAware->hash());

        // States that hash interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(HashInterface::class)
            ->willReturn(false);

        $simpleHashAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleHashAware->hash());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Hash interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testInputAwareTrait()
    {
        $simpleInputAware = new SimpleInputAware();
        $request = $this->createRequestMock();

        $stream = $this->createStreamMock();
        $stream->method('write')
            ->with('{"code":200,"status":"success","message":"Ok","data":[]}')
            ->willReturn(56);

        $request->method('getBody')
            ->willReturn($stream);

        $this->assertNull($simpleInputAware->input($request));

        $request->method('getHeader')
            ->with('content-type')
            ->willReturn(['multipart/form-data']);

        $this->assertNull($simpleInputAware->input($request, true));

        $request = $this->createRequestMock();

        $stream = $this->createStreamMock();
        $stream->method('getContents')
            ->willReturn('{"code":200,"status":"success","message":"Ok","data":[]}');

        $request->method('getBody')
            ->willReturn($stream);

        $request->method('getHeader')
            ->with('content-type')
            ->willReturn(['application/json']);

        $this->assertInstanceOf(\stdClass::class, $simpleInputAware->input($request));

        $request = $this->createRequestMock();

        $stream = $this->createStreamMock();
        $stream->method('getContents')
            ->willReturn('<body><code>200</code><status>success</status></body>');

        $request->method('getBody')
            ->willReturn($stream);

        $request->method('getHeader')
            ->with('content-type')
            ->willReturn(['application/xml']);

        $this->assertInstanceOf(\stdClass::class, $simpleInputAware->input($request));

        $request = $this->createRequestMock();

        $this->assertInstanceOf(\stdClass::class, $simpleInputAware->query($request));
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testLoggerAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleLoggerAware = new SimpleLoggerAware();
        $logger = $this->createLoggerMock();

        $container->method('has')
            ->with(LoggerInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $simpleLoggerAware->setContainer($container);
        $simpleLoggerAware->setLogger($logger);

        $this->assertInstanceOf(LoggerInterface::class,
            $simpleLoggerAware->getLogger());
        $this->assertInstanceOf(LoggerInterface::class,
            $simpleLoggerAware->logger());

        // States that logger interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(LoggerInterface::class)
            ->willReturn(false);

        $simpleLoggerAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleLoggerAware->logger());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Logger interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testValidatorAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleValidatorAware = new SimpleValidatorAware();
        $validator = $this->createValidatorMock();

        $container->method('has')
            ->with(ValidatorInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(ValidatorInterface::class)
            ->willReturn($validator);

        $simpleValidatorAware->setContainer($container);
        $simpleValidatorAware->setValidator($validator);

        $this->assertInstanceOf(ValidatorInterface::class,
            $simpleValidatorAware->getValidator());
        $this->assertInstanceOf(ValidatorInterface::class,
            $simpleValidatorAware->validator());

        // States that validator interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(ValidatorInterface::class)
            ->willReturn(false);

        $simpleValidatorAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleValidatorAware->validator());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Validator interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testSessionAwareTrait()
    {
        $container = $this->createContainerMock();

        $simpleSessionAware = new SimpleSessionAware();
        $session = $this->createSessionMock();

        $container->method('has')
            ->with(SessionInterface::class)
            ->willReturn(true);

        $container->method('get')
            ->with(SessionInterface::class)
            ->willReturn($session);

        $simpleSessionAware->setContainer($container);
        $simpleSessionAware->setSession($session);

        $this->assertInstanceOf(SessionInterface::class,
            $simpleSessionAware->getSession());
        $this->assertInstanceOf(SessionInterface::class,
            $simpleSessionAware->session());

        // States that session interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->with(SessionInterface::class)
            ->willReturn(false);

        $simpleSessionAware->setContainer($container);

        try {
            $this->assertInstanceOf(Exception::class,
                $simpleSessionAware->session());
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertEquals('Session interface not implemented.', $e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    /**
     * @return mixed
     */
    private function createAuthMock()
    {
        return $this->createMock(AuthInterface::class);
    }

    /**
     * @return mixed
     */
    private function createConfigMock()
    {
        return $this->createMock(ConfigInterface::class);
    }

    /**
     * @return mixed
     */
    private function createConsoleMock()
    {
        return $this->createMock(ConsoleInterface::class);
    }

    /**
     * @return mixed
     */
    private function createCookieMock()
    {
        return $this->createMock(CookieInterface::class);
    }

    /**
     * @return mixed
     */
    private function createDatabaseMockBuilder()
    {
        return $this->getMockBuilder(DatabaseInterface::class)
            ->setMethods([
                'database',
                'connection',
            ])
            ->getMock();
    }

    /**
     * @return mixed
     */
    private function createDatabaseMock()
    {
        return $this->createMock(DatabaseInterface::class);
    }

    /**
     * @return mixed
     */
    private function createFlashMock()
    {
        return $this->createMock(FlashInterface::class);
    }

    /**
     * @return mixed
     */
    private function createHashMock()
    {
        return $this->createMock(HashInterface::class);
    }

    /**
     * @return mixed
     */
    private function createLoggerMock()
    {
        return $this->createMock(LoggerInterface::class);
    }

    /**
     * @return mixed
     */
    private function createRequestMock()
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->getMock();
    }

    /**
     * @return mixed
     */
    private function createSessionMock()
    {
        return $this->createMock(SessionInterface::class);
    }

    /**
     * @return mixed
     */
    private function createStreamMock()
    {
        return $this->createMock(StreamInterface::class);
    }

    /**
     * @return mixed
     */
    private function createValidatorMock()
    {
        return $this->createMock(ValidatorInterface::class);
    }
}
