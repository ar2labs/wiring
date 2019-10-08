<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\SessionAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleSessionAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use SessionAwareTrait;
}
