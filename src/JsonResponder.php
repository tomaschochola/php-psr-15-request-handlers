<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @no-named-arguments
 */
readonly class JsonResponder
{
    private JsonWriter $jsonWriter;

    private ResponseFactoryInterface $responseFactory;

    public function __construct(JsonWriter $jsonWriter, ResponseFactoryInterface $responseFactory)
    {
        $this->jsonWriter = $jsonWriter;
        $this->responseFactory = $responseFactory;
    }

    #[NoDiscard()]
    public function createResponse(int $code, string $reasonPhrase, mixed $data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($code, $reasonPhrase);

        return $this->jsonWriter->write($response, $data);
    }
}
