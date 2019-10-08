<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\AuthAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleAuthAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use AuthAwareTrait;
}
