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

use function explode;
use function is_string;
use function sscanf;

/**
 * @no-named-arguments
 */
readonly class WithRequestCookiesMiddleware implements MiddlewareInterface
{
    #[NoDiscard()]
    #[Override()]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getHeaderLine('Cookie');

        if ($cookies === '') {
            return $handler->handle($request);
        }

        $result = [];

        foreach (explode(';', $cookies) as $cookie) {
            $key = null;
            $val = null;
            $scanned = sscanf($cookie, ' %[^=] = %[^;]', $key, $val);

            if ($scanned === 2 && is_string($key) && is_string($val)) {
                $result[$key][] = $val;
            }
        }

        if ($result !== []) {
            $request = $request->withCookieParams($result);
        }

        return $handler->handle($request);
    }
}
