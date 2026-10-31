<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function apache_request_headers;
use function assert;
use function is_string;

/**
 * @no-named-arguments
 */
readonly class WithRequestHeadersMiddleware implements MiddlewareInterface
{
    #[NoDiscard()]
    #[Override()]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        foreach (apache_request_headers() as $key => $value) {
            assert(is_string($value));

            $request = $request->withAddedHeader((string) $key, $value);
        }

        return $handler->handle($request);
    }
}
