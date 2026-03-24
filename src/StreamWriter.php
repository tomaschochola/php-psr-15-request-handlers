<?php

/**
 * @author Tomáš Chochola <tomaschochola@tomaschochola.cz>
 * @copyright © 2026 Tomáš Chochola <tomaschochola@tomaschochola.cz>
 *
 * @license CC-BY-ND-4.0
 *
 * @see {@link https://creativecommons.org/licenses/by-nd/4.0/} License
 * @see {@link https://github.com/tomaschochola} GitHub Profile
 * @see {@link https://github.com/sponsors/tomaschochola} GitHub Sponsors
 */

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
