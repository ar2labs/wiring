<?php

declare(strict_types=1);

namespace Wiring\Tests\Http\Helpers;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
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
     * @runInSeparateProcess
     * @throws \Exception
     *
     * @return void
     */
    public function testCookie()
    {
        $_COOKIE['test'] = '123';

        $this->assertIsBool(Cookie::set('test', '123'));
        $this->assertIsBool(Cookie::has('test'));
        $this->assertIsString(Cookie::get('test'));
        $this->assertIsBool(Cookie::forget('test'));
        $this->assertIsBool(Cookie::forget('test2'));
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
     * @throws \Exception
     *
     * @return void
     */
    public function testLoader()
    {
        $loader = new Loader();

        $this->assertInstanceOf(Loader::class, $loader->addPath('test'));
        $this->assertIsArray($loader->load());
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testMailer()
    {
        $mailerMock = $this->createMailerMock();
        $container = $this->createContainerMock();

        $view = $this->createViewStrategyMock();
        $view->method('engine')
            ->willReturn($view);

        $container = $this->createContainerMock();
        $container->method('get')
            ->with(ViewStrategyInterface::class)
            ->willReturn($view);

        $mailer = new Mailer($mailerMock, $container);
        $message = new Message($mailerMock);

        $this->assertInstanceOf(Message::class, $message->to('test@gmail.com'));
        $this->assertInstanceOf(Message::class, $message->subject('test'));

        $this->assertIsBool($mailer->send('template', [
            'test' => '123',
        ], function () {
            // Callback
        }));
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    public function testSession()
    {
        $this->assertIsString(Session::set('test', '123'));
        $this->assertIsBool(Session::has('test'));
        $this->assertIsString(Session::get('test'));
        $this->assertIsString(Session::get('test2'));
        $this->assertIsBool(Session::forget('test'));
        $this->assertIsBool(Session::forget('test2'));
    }

    /**
     * @return mixed
     */
    private function createMailerMock()
    {
        return $this->createMock(MailerInterface::class);
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
    private function createViewStrategyMock()
    {
        return $this->createMock(ViewStrategyInterface::class);
    }
}
