<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\ConsoleAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleConsoleAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ConsoleAwareTrait;
}
