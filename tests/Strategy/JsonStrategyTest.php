<?php

declare(strict_types=1);

namespace Wiring\Tests\Strategy;

use Wiring\Strategy\JsonStrategy;
use Wiring\Interfaces\JsonStrategyInterface;
use PHPUnit\Framework\TestCase;

final class JsonStrategyTest extends TestCase
{
    public function testRender()
    {
        $jsonStrategy = new JsonStrategy();
        $result = $jsonStrategy->render(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(JsonStrategy::class, $result);
    }

    public function testWrite()
    {
        $jsonStrategy = new JsonStrategy();
        $result = $jsonStrategy->write(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertInstanceOf(JsonStrategyInterface::class, $result);
    }
}
