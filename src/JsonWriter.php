<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Http\Message\MessageInterface;

/**
 * @no-named-arguments
 */
readonly class JsonWriter
{
    private JsonEncoder $jsonEncoder;

    private StreamWriter $streamWriter;

    public function __construct(StreamWriter $streamWriter, JsonEncoder $jsonEncoder)
    {
        $this->streamWriter = $streamWriter;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @template T of MessageInterface
     *
     * @param T $message
     *
     * @return T
     */
    #[NoDiscard()]
    public function write(MessageInterface $message, mixed $data): MessageInterface
    {
        $this->streamWriter->write($message->getBody(), $this->jsonEncoder->encode($data));

        return $message->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
