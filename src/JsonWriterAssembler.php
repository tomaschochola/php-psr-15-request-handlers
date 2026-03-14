<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;

use function assert;

readonly class JsonWriterAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): JsonWriter
    {
        $streamWriter = $container->get(StreamWriter::class);

        assert($streamWriter instanceof StreamWriter);

        return new JsonWriter($streamWriter);
    }
}
