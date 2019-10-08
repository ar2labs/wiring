<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\ContainerAwareTrait;

class SimpleContainerAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
}
