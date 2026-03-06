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
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RequestParseBodyException;

use function assert;
use function request_parse_body;

/**
 * @no-named-arguments
 */
readonly class WithRequestPayloadMiddleware implements MiddlewareInterface
{
    protected readonly ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    #[NoDiscard]
    public static function unload(ContainerInterface $container): self
    {
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($responseFactory instanceof ResponseFactoryInterface);

        return new self($responseFactory);
    }

    #[NoDiscard]
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $parsed = request_parse_body();
        } catch (RequestParseBodyException) {
            return $this->responseFactory->createResponse(415);
        }

        $body = $parsed[0] ?? [];
        $files = $parsed[1] ?? [];

        if ($body !== []) {
            $request = $request->withParsedBody($body);
        }

        if ($files !== []) {
            $request = $request->withUploadedFiles($files);
        }

        return $handler->handle($request);
    }
}
