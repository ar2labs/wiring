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
     */
    public function testConsole()
    {
        $console = new Console();

        $this->assertNull($console->log('test'));
        $this->assertNull($console->log(new \stdClass()));
        $this->assertNull($console->debug('test'));
        $this->assertNull($console->table('test'));
        $this->assertNull($console->info('test'));
        $this->assertNull($console->warn('test'));
        $this->assertNull($console->error('test'));
        $this->assertNull($console->trace('test'));
        $this->assertNull($console->dir(['test' => '123']));
        $this->assertNull($console->dirxml(['test' => '123']));
        $this->assertNull($console->assert('test', '123'));
        $this->assertNull($console->assert(true));
        $this->assertNull($console->clear());
        $this->assertNull($console->count());
        $this->assertNull($console->time('test'));
        $this->assertNull($console->timeend('test'));
        $this->assertNull($console->group('test'));
        $this->assertNull($console->groupend('test'));
    }

    /**
     * @runInSeparateProcess
     * @throws \Exception
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
     */
    public function testInfo()
    {
        $info = new Info();

        $this->assertIsString($info->phpinfo());
    }

    /**
     * @throws \Exception
     */
    public function testLoader()
    {
        $loader = new Loader();

        $this->assertNull($loader->addPath('test'));
        $this->assertIsArray($loader->load());
    }

    /**
     * @throws \Exception
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

        $this->assertNull($message->to('test@gmail.com'));
        $this->assertNull($message->subject('test'));

        $this->assertIsBool($mailer->send('template', [
            'test' => '123',
        ], function () {
            // Callback
        }));
    }

    public function testSession()
    {
        $this->assertIsString(Session::set('test', '123'));
        $this->assertIsBool(Session::has('test'));
        $this->assertIsString(Session::get('test'));
        $this->assertIsString(Session::get('test2'));
        $this->assertNull(Session::forget('test'));
    }

    private function createMailerMock()
    {
        return $this->createMock(MailerInterface::class);
    }

    private function createContainerMock()
    {
        return $this->createMock(ContainerInterface::class);
    }

    private function createViewStrategyMock()
    {
        return $this->createMock(ViewStrategyInterface::class);
    }
}
