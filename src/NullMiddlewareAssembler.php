<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;

readonly class NullMiddlewareAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): NullMiddleware
    {
        return new NullMiddleware();
    }
}
