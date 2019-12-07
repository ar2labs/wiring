<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\InputAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleInputAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use InputAwareTrait;
}
