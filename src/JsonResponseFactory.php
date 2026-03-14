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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @no-named-arguments
 */
readonly class JsonResponseFactory
{
    private readonly JsonWriter $jsonWriter;

    private readonly ResponseFactoryInterface $responseFactory;

    public function __construct(JsonWriter $jsonWriter, ResponseFactoryInterface $responseFactory)
    {
        $this->jsonWriter = $jsonWriter;
        $this->responseFactory = $responseFactory;
    }

    #[NoDiscard]
    public function createResponse(int $code, string $reasonPhrase, mixed $data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($code, $reasonPhrase);

        $response = $this->jsonWriter->write($response, $data);

        return $response;
    }
}
