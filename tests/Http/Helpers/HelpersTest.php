<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Helpers;

use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use UnexpectedValueException;
use Wiring\Http\Helpers\Console;
use Wiring\Http\Helpers\Cookie;
use Wiring\Http\Helpers\Info;
use Wiring\Http\Helpers\Loader;
use Wiring\Http\Helpers\Mailer;
use Wiring\Http\Helpers\Mailtrap\Message;
use Wiring\Http\Helpers\Session;
use Wiring\Interfaces\MailerInterface;
use Wiring\Interfaces\ViewStrategyInterface;

final class HelpersTest extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testConsole()
    {
        $console = new Console();

        $this->assertInstanceOf(Console::class, $console->log('test'));
        $this->assertInstanceOf(Console::class, $console->log(new \stdClass()));
        $this->assertInstanceOf(Console::class, $console->debug('test'));
        $this->assertInstanceOf(Console::class, $console->table('test'));
        $this->assertInstanceOf(Console::class, $console->info('test'));
        $this->assertInstanceOf(Console::class, $console->warn('test'));
        $this->assertInstanceOf(Console::class, $console->error('test'));
        $this->assertInstanceOf(Console::class, $console->trace('test'));
        $this->assertInstanceOf(Console::class, $console->dir(['test' => '123']));
        $this->assertInstanceOf(Console::class, $console->dirxml(['test' => '123']));
        $this->assertInstanceOf(Console::class, $console->assert('test', '123'));
        $this->assertInstanceOf(Console::class, $console->assert(true));
        $this->assertInstanceOf(Console::class, $console->clear());
        $this->assertInstanceOf(Console::class, $console->count());
        $this->assertInstanceOf(Console::class, $console->time('test'));
        $this->assertInstanceOf(Console::class, $console->timeend('test'));
        $this->assertInstanceOf(Console::class, $console->group('test'));
        $this->assertInstanceOf(Console::class, $console->groupend('test'));
    }

    /**
     * @return void
     */
    public function testConsoleHandlesUnencodableValues()
    {
        $resource = fopen('phpunit.xml.dist', 'r');
        $this->assertIsResource($resource);

        try {
            $this->assertInstanceOf(Console::class, (new Console())->log($resource));
        } finally {
            fclose($resource);
        }
    }

    /**
     * @return void
     */
    public function testConsoleEscapesJavaScriptStringContext()
    {
        $_SESSION = [];

        (new Console())->log("';alert(1);//<script>");

        /** @var array<string, mixed> $session */
        $session = $_SESSION;
        $consoleLog = $session[Console::CONSOLE_LOG] ?? null;

        $this->assertIsArray($consoleLog);
        $output = $consoleLog[0] ?? '';

        $this->assertIsString($output);
        $this->assertStringContainsString('\\u0027', $output);
        $this->assertStringContainsString('\\u003Cscript\\u003E', $output);
        $this->assertStringNotContainsString("';alert(1);//<script>", $output);
    }

    /**
     * @return void
     */
    public function testConsoleReplacesInvalidSessionLog()
    {
        $this->corruptConsoleLogSession();

        (new Console())->log('test');

        $consoleLog = $_SESSION[Console::CONSOLE_LOG] ?? null;

        $this->assertIsArray($consoleLog);
        $this->assertCount(1, $consoleLog);
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
     *
     * @return void
     */
    public function testCookie()
    {
        $_COOKIE['test'] = '123';

        $this->assertTrue(Cookie::set('test', '123'));
        $this->assertTrue(Cookie::has('test'));
        $this->assertIsString(Cookie::get('test'));
        $this->assertTrue(Cookie::forget('test'));
        $this->assertFalse(Cookie::forget('test2'));
    }

    /**
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testCookieReturnsEmptyStringForUnsupportedValues()
    {
        $_COOKIE['unsupported'] = 123;

        $this->assertSame('', Cookie::get('unsupported'));
    }

    /**
     * @return void
     */
    public function testCookieOptionsUseHardenedDefaults()
    {
        $cookie = new class () extends Cookie {
            /**
             * @return array{expires: int, path: string, domain?: string, secure: bool, httponly: bool, samesite: 'Lax'}
             */
            public static function options(
                int $expiry,
                string $path,
                string $domain,
                bool $secure,
                bool $httponly
            ): array {
                return self::createCookieOptions($expiry, $path, $domain, $secure, $httponly);
            }
        };

        unset($_SERVER['HTTPS'], $_SERVER['SERVER_PORT']);

        $options = $cookie::options(0, '/', '', false, true);

        self::assertFalse($options['secure']);
        self::assertTrue($options['httponly']);
        self::assertSame('Lax', $options['samesite']);
        self::assertArrayNotHasKey('domain', $options);

        $_SERVER['HTTPS'] = 'on';
        $httpsOptions = $cookie::options(0, '/', 'example.com', false, true);

        self::assertTrue($httpsOptions['secure']);
        self::assertSame('example.com', $httpsOptions['domain'] ?? null);
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testInfo()
    {
        $info = new Info();

        $this->assertIsString($info->phpinfo());
    }

    /**
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testInfoUsesDefinedApplicationVersion()
    {
        define('APP_VERSION', '9.9.9');

        $this->assertIsString((new Info())->phpinfo());
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testLoader()
    {
        $loader = new Loader();

        $this->assertInstanceOf(Loader::class, $loader->addPath('test'));
        $this->assertSame([], $loader->load());
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testMailer()
    {
        $mailerMock = new class () implements MailerInterface {
            private string $subject = '';
            private string $body = '';

            /** @var array<int, string> */
            public array $addresses = [];

            public function __set(string $name, string $value): void
            {
                if ($name === 'Subject') {
                    $this->subject = $value;
                }

                if ($name === 'Body') {
                    $this->body = $value;
                }
            }

            public function __get(string $name): string
            {
                if ($name === 'Subject') {
                    return $this->subject;
                }

                if ($name === 'Body') {
                    return $this->body;
                }

                throw new UnexpectedValueException('Unknown mailer property.');
            }

            public function addAddress(string $email): void
            {
                $this->addresses[] = $email;
            }

            public function send(): bool
            {
                return true;
            }
        };

        $view = $this->createViewStrategyMock();
        $view->method('engine')
            ->willReturn($view);
        $view->method('render')
            ->willReturn('<p>test</p>');

        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($view);

        $mailer = new Mailer($mailerMock, $container);
        $message = new Message($mailerMock);

        $this->assertInstanceOf(Message::class, $message->to('test@gmail.com'));
        $this->assertInstanceOf(Message::class, $message->subject('test'));
        $this->assertSame(['test@gmail.com'], $mailerMock->addresses);
        $this->assertSame('test', $mailerMock->Subject);

        $this->assertTrue($mailer->send('template', [
            'test' => '123',
        ], function () {
            // Callback
        }));
        $this->assertSame('<p>test</p>', $mailerMock->Body);
    }

    /**
     * @return void
     */
    public function testMailerRequiresMailerInterface()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Mailer interface not implemented.');

        $mailer = new Mailer(new stdClass(), $this->createContainerMock());

        $this->assertInstanceOf(Mailer::class, $mailer);
    }

    /**
     * @return void
     */
    public function testMailtrapMessageRequiresMailerInterface()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Mailer interface not implemented.');

        $message = new Message(new stdClass());

        $this->assertInstanceOf(Message::class, $message);
    }

    /**
     * @return void
     */
    public function testMailerRequiresViewStrategy()
    {
        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn(new stdClass());

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('View strategy interface not implemented.');

        (new Mailer($this->createMailer(), $container))->send('template', [], static function (): void {
            self::fail('The callback should not run when the view service is invalid.');
        });
    }

    /**
     * @return void
     */
    public function testMailerRequiresRenderableEngine()
    {
        $view = $this->createViewStrategyMock();
        $view->method('engine')
            ->willReturn(new stdClass());

        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($view);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Template engine must provide a render method.');

        (new Mailer($this->createMailer(), $container))->send('template', [], static function (): void {
            self::fail('The callback should not run when the renderer is invalid.');
        });
    }

    /**
     * @return void
     */
    public function testMailerRequiresStringRenderResult()
    {
        $engine = new class () {
            /**
             * @param array<string, mixed> $data
             *
             * @return array<string, mixed>
             */
            public function render(string $template, array $data): array
            {
                return [
                    'template' => $template,
                    'data' => $data,
                ];
            }
        };

        $view = $this->createViewStrategyMock();
        $view->method('engine')
            ->willReturn($engine);

        $container = $this->createContainerMock();
        $container->method('get')
            ->willReturn($view);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Template render must return a string.');

        (new Mailer($this->createMailer(), $container))->send('template', [], static function (): void {
            self::fail('The callback should not run when template rendering fails.');
        });
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testSession()
    {
        $this->assertIsString(Session::set('test', '123'));
        $this->assertTrue(Session::has('test'));
        $this->assertIsString(Session::get('test'));
        $this->assertIsString(Session::get('test2'));
        $this->assertTrue(Session::forget('test'));
        $this->assertFalse(Session::forget('test2'));
    }

    private function createContainerMock(): ContainerInterface&Stub
    {
        return $this->createStub(ContainerInterface::class);
    }

    private function corruptConsoleLogSession(): void
    {
        $_SESSION = [
            Console::CONSOLE_LOG => 'not-a-list',
        ];
    }

    private function createViewStrategyMock(): ViewStrategyInterface&Stub
    {
        return $this->createStub(ViewStrategyInterface::class);
    }

    private function createMailer(): MailerInterface
    {
        return new class () implements MailerInterface {
            /** @var array<string, string> */
            private array $fields = [
                'Subject' => '',
                'Body' => '',
            ];

            public function __set(string $name, string $value): void
            {
                $this->fields[$name] = $value;
            }

            public function __get(string $name): string
            {
                return $this->fields[$name] ?? '';
            }

            public function addAddress(string $email): void
            {
                unset($email);
            }

            public function send(): bool
            {
                return true;
            }
        };
    }
}
