<?php

declare(strict_types=1);

namespace Wiring\Tests\Traits;

use Wiring\Interfaces\ContainerAwareInterface;
use Wiring\Traits\CookieAwareTrait;
use Wiring\Traits\ContainerAwareTrait;

class SimpleCookieAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    use CookieAwareTrait;
}
