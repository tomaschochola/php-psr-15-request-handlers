<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use Psr\Http\Message\StreamInterface;
use UnexpectedValueException;

use function mb_strlen;

/**
 * @no-named-arguments
 */
readonly class StreamWriter
{
    public function write(StreamInterface $stream, string $data): void
    {
        $written = $stream->write($data);

        if ($written !== mb_strlen($data, '8bit')) {
            throw new UnexpectedValueException($stream::class . '->write');
        }
    }
}
