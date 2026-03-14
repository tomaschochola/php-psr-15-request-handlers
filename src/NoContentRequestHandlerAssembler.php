<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use function assert;

readonly class NoContentRequestHandlerAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): NoContentRequestHandler
    {
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($responseFactory instanceof ResponseFactoryInterface);

        return new NoContentRequestHandler($responseFactory);
    }
}
