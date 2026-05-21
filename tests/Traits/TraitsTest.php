<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use BadMethodCallException;
use Exception;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use stdClass;
use UnexpectedValueException;
use Wiring\Interfaces\AuthInterface;
use Wiring\Interfaces\ConfigInterface;
use Wiring\Interfaces\ConsoleInterface;
use Wiring\Interfaces\ContainerAwareInterface;
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($auth);

        $simpleAuthAware->setContainer($container);
        $simpleAuthAware->setAuth($auth);

        $this->assertInstanceOf(
            AuthInterface::class,
            $simpleAuthAware->getAuth()
        );
        $this->assertInstanceOf(
            AuthInterface::class,
            $simpleAuthAware->auth()
        );

        // States that auth interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleAuthAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleAuthAware->auth()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Auth interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($config);

        $simpleConfigAware->setContainer($container);
        $simpleConfigAware->setConfig($config);

        $this->assertInstanceOf(
            ConfigInterface::class,
            $simpleConfigAware->getConfig()
        );
        $this->assertInstanceOf(
            ConfigInterface::class,
            $simpleConfigAware->config()
        );
        $this->assertNull($simpleConfigAware->lang('test'));

        // States that config interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleConfigAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleConfigAware->config()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Config interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($console);

        $simpleConsoleAware->setContainer($container);
        $simpleConsoleAware->setConsole($console);

        $this->assertInstanceOf(
            ConsoleInterface::class,
            $simpleConsoleAware->getConsole()
        );
        $this->assertInstanceOf(
            ConsoleInterface::class,
            $simpleConsoleAware->console()
        );

        // States that console interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleConsoleAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleConsoleAware->console()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Console interface not implemented.', $e->getMessage());
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
            $this->assertInstanceOf(
                BadMethodCallException::class,
                $simpleContainerAware->get('test')
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(BadMethodCallException::class, $e);
            $this->assertSame('Method get does not exist.', $e->getMessage());
        }

        try {
            $this->assertInstanceOf(
                BadMethodCallException::class,
                $simpleContainerAware->has('test')
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(BadMethodCallException::class, $e);
            $this->assertSame('Method has does not exist.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($cookie);

        $simpleCookieAware->setContainer($container);
        $simpleCookieAware->setCookie($cookie);

        $this->assertInstanceOf(
            CookieInterface::class,
            $simpleCookieAware->getCookie()
        );
        $this->assertInstanceOf(
            CookieInterface::class,
            $simpleCookieAware->cookie()
        );

        // States that cookie interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleCookieAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleCookieAware->cookie()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Cookie interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($database);

        $simpleDatabaseAware->setContainer($container);

        $this->assertInstanceOf(
            DatabaseInterface::class,
            $simpleDatabaseAware->database()
        );

        $database = $this->createDatabaseMock();

        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(true);

        $container->method('get')
            ->willReturn($database);

        $simpleDatabaseAware->setContainer($container);

        $database->method('connection')
            ->willReturnSelf();

        $container->method('get')
            ->willReturn($database);

        $this->assertInstanceOf(
            DatabaseInterface::class,
            $simpleDatabaseAware->database('default')
        );

        // States that database interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleDatabaseAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleDatabaseAware->database()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Database interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($flash);

        $simpleFlashAware->setContainer($container);
        $simpleFlashAware->setFlash($flash);

        $this->assertInstanceOf(
            FlashInterface::class,
            $simpleFlashAware->getFlash()
        );
        $this->assertInstanceOf(
            FlashInterface::class,
            $simpleFlashAware->flash()
        );

        // States that flash interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleFlashAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleFlashAware->flash()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Flash interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($hash);

        $simpleHashAware->setContainer($container);
        $simpleHashAware->setHash($hash);

        $this->assertInstanceOf(
            HashInterface::class,
            $simpleHashAware->getHash()
        );
        $this->assertInstanceOf(
            HashInterface::class,
            $simpleHashAware->hash()
        );

        // States that hash interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleHashAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleHashAware->hash()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Hash interface not implemented.', $e->getMessage());
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
            ->willReturn(56);

        $request->method('getBody')
            ->willReturn($stream);

        $this->assertSame('', $simpleInputAware->input($request));

        $request->method('getHeader')
            ->willReturn(['multipart/form-data']);

        $this->assertNull($simpleInputAware->input($request, true));

        $request = $this->createRequestMock();

        $stream = $this->createStreamMock();
        $stream->method('getContents')
            ->willReturn('{"code":200,"status":"success","message":"Ok","data":[]}');

        $request->method('getBody')
            ->willReturn($stream);

        $request->method('getHeader')
            ->willReturn(['application/json']);

        $this->assertInstanceOf(\stdClass::class, $simpleInputAware->input($request));

        $request = $this->createRequestMock();

        $stream = $this->createStreamMock();
        $stream->method('getContents')
            ->willReturn('<body><code>200</code><status>success</status></body>');

        $request->method('getBody')
            ->willReturn($stream);

        $request->method('getHeader')
            ->willReturn(['application/xml']);

        $this->assertInstanceOf(\stdClass::class, $simpleInputAware->input($request));

        $request = $this->createRequestMock();

        $this->assertInstanceOf(\stdClass::class, $simpleInputAware->query($request));

        $request = $this->createRequestMock();
        $request->method('getHeader')
            ->willReturn(['application/x-www-form-urlencoded']);
        $request->method('getParsedBody')
            ->willReturn(['name' => 'value']);
        $request->method('getBody')
            ->willReturn($this->createStreamMock());

        $this->assertEquals((object) ['name' => 'value'], $simpleInputAware->input($request));
    }

    /**
     * @return void
     */
    public function testInputAwareTraitDoesNotExpandExternalXmlEntities()
    {
        $secretFile = tempnam(sys_get_temp_dir(), 'wiring-xxe-');
        $this->assertIsString($secretFile);
        file_put_contents($secretFile, 'TOP_SECRET_XXE_VALUE');

        $fileUri = str_replace('\\', '/', $secretFile);
        $fileUri = DIRECTORY_SEPARATOR === '\\' ? 'file:///' . $fileUri : 'file://' . $fileUri;

        $xml = '<!DOCTYPE body [<!ENTITY xxe SYSTEM "' . $fileUri . '">]>' .
            '<body><value>&xxe;</value></body>';

        $request = $this->createRequestMock();
        $stream = $this->createStreamMock();
        $stream->method('getContents')
            ->willReturn($xml);

        $request->method('getBody')
            ->willReturn($stream);
        $request->method('getHeader')
            ->willReturn(['application/xml']);

        try {
            $result = (new SimpleInputAware())->input($request, true);
            $encodedResult = json_encode($result);

            $this->assertIsString($encodedResult);
            $this->assertStringNotContainsString('TOP_SECRET_XXE_VALUE', $encodedResult);
        } finally {
            unlink($secretFile);
        }
    }

    /**
     * @return void
     */
    public function testAwareTraitsValidateContainerServiceTypes()
    {
        $authAware = new SimpleAuthAware();
        $this->assertInvalidAwareService($authAware, static fn () => $authAware->auth(), 'Auth interface not implemented.');

        $configAware = new SimpleConfigAware();
        $this->assertInvalidAwareService($configAware, static fn () => $configAware->config(), 'Config interface not implemented.');

        $consoleAware = new SimpleConsoleAware();
        $this->assertInvalidAwareService($consoleAware, static fn () => $consoleAware->console(), 'Console interface not implemented.');

        $cookieAware = new SimpleCookieAware();
        $this->assertInvalidAwareService($cookieAware, static fn () => $cookieAware->cookie(), 'Cookie interface not implemented.');

        $databaseAware = new SimpleDatabaseAware();
        $this->assertInvalidAwareService($databaseAware, static fn () => $databaseAware->database(), 'Database interface not implemented.');

        $flashAware = new SimpleFlashAware();
        $this->assertInvalidAwareService($flashAware, static fn () => $flashAware->flash(), 'Flash interface not implemented.');

        $hashAware = new SimpleHashAware();
        $this->assertInvalidAwareService($hashAware, static fn () => $hashAware->hash(), 'Hash interface not implemented.');

        $loggerAware = new SimpleLoggerAware();
        $this->assertInvalidAwareService($loggerAware, static fn () => $loggerAware->logger(), 'Logger interface not implemented.');

        $sessionAware = new SimpleSessionAware();
        $this->assertInvalidAwareService($sessionAware, static fn () => $sessionAware->session(), 'Session interface not implemented.');

        $validatorAware = new SimpleValidatorAware();
        $this->assertInvalidAwareService($validatorAware, static fn () => $validatorAware->validator(), 'Validator interface not implemented.');
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($logger);

        $simpleLoggerAware->setContainer($container);
        $simpleLoggerAware->setLogger($logger);

        $this->assertInstanceOf(
            LoggerInterface::class,
            $simpleLoggerAware->getLogger()
        );
        $this->assertInstanceOf(
            LoggerInterface::class,
            $simpleLoggerAware->logger()
        );

        // States that logger interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleLoggerAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleLoggerAware->logger()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Logger interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($validator);

        $simpleValidatorAware->setContainer($container);
        $simpleValidatorAware->setValidator($validator);

        $this->assertInstanceOf(
            ValidatorInterface::class,
            $simpleValidatorAware->getValidator()
        );
        $this->assertInstanceOf(
            ValidatorInterface::class,
            $simpleValidatorAware->validator()
        );

        // States that validator interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleValidatorAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleValidatorAware->validator()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Validator interface not implemented.', $e->getMessage());
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
            ->willReturn(true);

        $container->method('get')
            ->willReturn($session);

        $simpleSessionAware->setContainer($container);
        $simpleSessionAware->setSession($session);

        $this->assertInstanceOf(
            SessionInterface::class,
            $simpleSessionAware->getSession()
        );
        $this->assertInstanceOf(
            SessionInterface::class,
            $simpleSessionAware->session()
        );

        // States that session interface has not been implemented
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(false);

        $simpleSessionAware->setContainer($container);

        try {
            $this->assertInstanceOf(
                Exception::class,
                $simpleSessionAware->session()
            );
        } catch (Exception $e) {
            $this->assertInstanceOf(Exception::class, $e);
            $this->assertSame('Session interface not implemented.', $e->getMessage());
        }
    }

    private function createContainerMock(): ContainerInterface&Stub
    {
        return $this->createStub(ContainerInterface::class);
    }

    private function createAuthMock(): AuthInterface&Stub
    {
        return $this->createStub(AuthInterface::class);
    }

    private function createConfigMock(): ConfigInterface&Stub
    {
        return $this->createStub(ConfigInterface::class);
    }

    private function createConsoleMock(): ConsoleInterface&Stub
    {
        return $this->createStub(ConsoleInterface::class);
    }

    private function createCookieMock(): CookieInterface&Stub
    {
        return $this->createStub(CookieInterface::class);
    }

    private function createDatabaseMock(): DatabaseInterface&Stub
    {
        return $this->createStub(DatabaseInterface::class);
    }

    private function createFlashMock(): FlashInterface&Stub
    {
        return $this->createStub(FlashInterface::class);
    }

    private function createHashMock(): HashInterface&Stub
    {
        return $this->createStub(HashInterface::class);
    }

    private function createLoggerMock(): LoggerInterface&Stub
    {
        return $this->createStub(LoggerInterface::class);
    }

    private function createRequestMock(): ServerRequestInterface&Stub
    {
        return $this->createStub(ServerRequestInterface::class);
    }

    private function createSessionMock(): SessionInterface&Stub
    {
        return $this->createStub(SessionInterface::class);
    }

    private function createStreamMock(): StreamInterface&Stub
    {
        return $this->createStub(StreamInterface::class);
    }

    private function createValidatorMock(): ValidatorInterface&Stub
    {
        return $this->createStub(ValidatorInterface::class);
    }

    /**
     * @param callable(): mixed $call
     */
    private function assertInvalidAwareService(ContainerAwareInterface $aware, callable $call, string $message): void
    {
        $container = $this->createContainerMock();
        $container->method('has')
            ->willReturn(true);
        $container->method('get')
            ->willReturn(new stdClass());

        $aware->setContainer($container);

        try {
            $call();
            $this->fail('Expected invalid service type to fail.');
        } catch (UnexpectedValueException $e) {
            $this->assertSame($message, $e->getMessage());
        }
    }
}
