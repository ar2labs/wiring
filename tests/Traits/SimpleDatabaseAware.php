<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\DatabaseAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleDatabaseAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use DatabaseAwareTrait;
}
