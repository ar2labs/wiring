<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\ContainerAwareTrait;
use Wiring\Traits\ValidatorAwareTrait;

class SimpleValidatorAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use ValidatorAwareTrait;
}
