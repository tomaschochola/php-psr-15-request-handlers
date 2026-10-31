<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use Throwable;

/**
 * @no-named-arguments
 */
interface HttpThrowableInterface extends Throwable
{
    public int $status { get; }
}
