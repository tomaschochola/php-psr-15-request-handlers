<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

use function assert;

readonly class JsonResponseFactoryAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): JsonResponseFactory
    {
        $jsonWriter = $container->get(JsonWriter::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($jsonWriter instanceof JsonWriter);
        assert($responseFactory instanceof ResponseFactoryInterface);

        return new JsonResponseFactory($jsonWriter, $responseFactory);
    }
}
