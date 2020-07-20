<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\ConfigAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleConfigAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ConfigAwareTrait;
}
