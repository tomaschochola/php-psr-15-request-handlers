<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Container\ContainerInterface;

readonly class StreamWriterAssembler
{
    #[NoDiscard]
    public static function assemble(ContainerInterface $container): StreamWriter
    {
        return new StreamWriter();
    }
}
