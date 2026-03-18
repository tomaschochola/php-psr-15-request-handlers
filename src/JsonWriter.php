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

use function assert;

/**
 * @no-named-arguments
 */
readonly class JsonWriter
{
    private readonly StreamWriter $streamWriter;

    private readonly JsonEncoder $jsonEncoder;

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $streamWriter = $container->get(StreamWriter::class);
        $jsonEncoder = $container->get(JsonEncoder::class);

        assert($streamWriter instanceof StreamWriter);
        assert($jsonEncoder instanceof JsonEncoder);

        return new self($streamWriter, $jsonEncoder);
    }

    public function __construct(StreamWriter $streamWriter, JsonEncoder $jsonEncoder)
    {
        $this->streamWriter = $streamWriter;
        $this->jsonEncoder = $jsonEncoder;
    }

    #[NoDiscard]
    public function write(MessageInterface $message, mixed $data): MessageInterface
    {
        $this->streamWriter->write($message->getBody(), $this->jsonEncoder->encode($data));

        return $message->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
