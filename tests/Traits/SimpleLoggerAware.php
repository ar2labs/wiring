<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\ContainerAwareTrait;
use Wiring\Traits\LoggerAwareTrait;

class SimpleLoggerAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use LoggerAwareTrait;
}
