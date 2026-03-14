<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;

use function assert;

readonly class RouteRequestHandlerAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): RouteRequestHandler
    {
        $deregistrator = $container->get(RouteMatcher::class);
        $runner = $container->get(PipelineRequestHandler::class);

        assert($deregistrator instanceof RouteMatcher);
        assert($runner instanceof PipelineRequestHandler);

        return new RouteRequestHandler($deregistrator, $runner);
    }
}
