<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use BadMethodCallException;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Wiring\Interfaces\AuthInterface;
use Wiring\Interfaces\ConfigInterface;
use Wiring\Interfaces\ConsoleInterface;
use Wiring\Interfaces\CookieInterface;
use Wiring\Interfaces\DatabaseInterface;
use Wiring\Interfaces\FlashInterface;
use Wiring\Interfaces\HashInterface;
use Wiring\Interfaces\SessionInterface;

final class TraitsTest extends TestCase
{
    /**
     * @throws \Exception
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

        $this->assertInstanceOf(SimpleAuthAware::class,
            $simpleAuthAware->setAuth($auth));
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

        $this->assertInstanceOf(SimpleConfigAware::class,
            $simpleConfigAware->setConfig($config));
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

        $this->assertInstanceOf(SimpleConsoleAware::class,
            $simpleConsoleAware->setConsole($console));
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
     * @throws \BadMethodCallException
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

        $this->assertInstanceOf(SimpleCookieAware::class,
            $simpleCookieAware->setCookie($cookie));
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

        $this->assertInstanceOf(SimpleDatabaseAware::class,
            $simpleDatabaseAware->setDatabase($database));
        $this->assertInstanceOf(DatabaseInterface::class,
            $simpleDatabaseAware->getDatabase());
        $this->assertInstanceOf(DatabaseInterface::class,
            $simpleDatabaseAware->database());

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

        $this->assertInstanceOf(SimpleFlashAware::class,
            $simpleFlashAware->setFlash($flash));
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

        $this->assertInstanceOf(SimpleHashAware::class,
            $simpleHashAware->setHash($hash));
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

        $this->assertInstanceOf(SimpleLoggerAware::class,
            $simpleLoggerAware->setLogger($logger));
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

        $this->assertInstanceOf(SimpleSessionAware::class,
            $simpleSessionAware->setSession($session));
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

    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    private function createAuthMock()
    {
        return $this->createMock(AuthInterface::class);
    }

    private function createConfigMock()
    {
        return $this->createMock(ConfigInterface::class);
    }

    private function createConsoleMock()
    {
        return $this->createMock(ConsoleInterface::class);
    }

    private function createCookieMock()
    {
        return $this->createMock(CookieInterface::class);
    }

    private function createDatabaseMock()
    {
        return $this->createMock(DatabaseInterface::class);
    }

    private function createFlashMock()
    {
        return $this->createMock(FlashInterface::class);
    }

    private function createHashMock()
    {
        return $this->createMock(HashInterface::class);
    }

    private function createLoggerMock()
    {
        return $this->createMock(LoggerInterface::class);
    }

    private function createSessionMock()
    {
        return $this->createMock(SessionInterface::class);
    }
}
