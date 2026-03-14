<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use function assert;

readonly class OkRequestHandlerAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): OkRequestHandler
    {
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($responseFactory instanceof ResponseFactoryInterface);

        return new OkRequestHandler($responseFactory);
    }
}
