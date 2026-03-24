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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;
use function is_object;

/**
 * @no-named-arguments
 */
readonly class RequireParsedBodyMiddleware implements MiddlewareInterface
{
    private readonly ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    #[NoDiscard]
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();

        if ($parsedBody === null) {
            return $this->reject($request, $handler);
        }

        if (is_array($parsedBody) && $parsedBody === []) {
            return $this->reject($request, $handler);
        }

        if (is_object($parsedBody) && (array) $parsedBody === []) {
            return $this->reject($request, $handler);
        }

        return $handler->handle($request);
    }

    #[NoDiscard]
    protected function reject(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->responseFactory->createResponse(415);
    }
}
