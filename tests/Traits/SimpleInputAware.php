<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\ContainerAwareTrait;
use Wiring\Traits\InputAwareTrait;

class SimpleInputAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use InputAwareTrait;
}
