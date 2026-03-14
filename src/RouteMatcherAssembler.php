<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;

use function assert;

readonly class RouteMatcherAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): RouteMatcher
    {
        $registry = $container->get(RouteSettingsInterface::class);

        assert($registry instanceof RouteSettingsInterface);

        return new RouteMatcher($registry);
    }
}
