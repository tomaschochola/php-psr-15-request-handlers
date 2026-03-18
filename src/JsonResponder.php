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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

use function assert;

/**
 * @no-named-arguments
 */
readonly class JsonResponder
{
    private readonly JsonWriter $jsonWriter;

    private readonly ResponseFactoryInterface $responseFactory;

    public function __construct(JsonWriter $jsonWriter, ResponseFactoryInterface $responseFactory)
    {
        $this->jsonWriter = $jsonWriter;
        $this->responseFactory = $responseFactory;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $jsonWriter = $container->get(JsonWriter::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($jsonWriter instanceof JsonWriter);
        assert($responseFactory instanceof ResponseFactoryInterface);

        return new self($jsonWriter, $responseFactory);
    }

    #[NoDiscard]
    public function createResponse(int $code, string $reasonPhrase, mixed $data): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($code, $reasonPhrase);

        return $this->jsonWriter->write($response, $data);
    }
}
