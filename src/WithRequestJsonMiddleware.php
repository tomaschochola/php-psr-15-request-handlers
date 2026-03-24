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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function is_array;
use function json_decode;

use const JSON_BIGINT_AS_STRING;
use const JSON_INVALID_UTF8_SUBSTITUTE;

/**
 * @no-named-arguments
 */
readonly class WithRequestJsonMiddleware implements MiddlewareInterface
{
    #[NoDiscard]
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $body = (string) $request->getBody();

        if ($body === '') {
            return $handler->handle($request);
        }

        $parsedBody = json_decode($body, true, 512, JSON_INVALID_UTF8_SUBSTITUTE | JSON_BIGINT_AS_STRING);

        if (!is_array($parsedBody)) {
            return $this->reject($request, $handler);
        }

        if ($parsedBody !== []) {
            $request = $request->withParsedBody($parsedBody);
        }

        return $handler->handle($request);
    }

    #[NoDiscard]
    protected function reject(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }
}
