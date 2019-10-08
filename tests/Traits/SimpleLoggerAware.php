<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\LoggerAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleLoggerAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use LoggerAwareTrait;
}
