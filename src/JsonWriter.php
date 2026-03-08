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

use NoDiscard;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\MessageInterface;
use UnexpectedValueException;

use function assert;
use function is_string;
use function json_encode;

use const JSON_INVALID_UTF8_SUBSTITUTE;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * @no-named-arguments
 */
readonly class JsonWriter
{
    protected readonly StreamWriter $streamWriter;

    public function __construct(StreamWriter $streamWriter)
    {
        $this->streamWriter = $streamWriter;
    }

    #[NoDiscard]
    public static function unload(ContainerInterface $container): self
    {
        $streamWriter = $container->get(StreamWriter::class);

        assert($streamWriter instanceof StreamWriter);

        return new self($streamWriter);
    }

    #[NoDiscard]
    public function encode(mixed $data): string
    {
        $encoded = json_encode($data, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($encoded)) {
            throw new UnexpectedValueException('json_encode');
        }

        return $encoded;
    }

    #[NoDiscard]
    public function write(MessageInterface $message, mixed $data): MessageInterface
    {
        $this->streamWriter->write($message->getBody(), $this->encode($data));

        return $message->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
