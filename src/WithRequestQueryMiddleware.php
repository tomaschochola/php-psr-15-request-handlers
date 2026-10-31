<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function parse_str;

/**
 * @no-named-arguments
 */
readonly class WithRequestQueryMiddleware implements MiddlewareInterface
{
    #[NoDiscard()]
    #[Override()]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $query = $request->getUri()->getQuery();

        if ($query === '') {
            return $handler->handle($request);
        }

        $result = [];

        parse_str($query, $result);

        if ($result !== []) {
            $request = $request->withQueryParams($result);
        }

        return $handler->handle($request);
    }
}
