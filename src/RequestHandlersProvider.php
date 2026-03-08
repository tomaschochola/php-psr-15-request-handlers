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

use IteratorAggregate;
use NoDiscard;
use Override;
use Psr\Http\Server\RequestHandlerInterface;
use Traversable;

/**
 * @no-named-arguments
 *
 * @implements IteratorAggregate<mixed, mixed>
 */
readonly class RequestHandlersProvider implements IteratorAggregate
{
    #[NoDiscard]
    #[Override]
    public function getIterator(): Traversable
    {
        yield ErrorHandlerMiddleware::class => [ErrorHandlerMiddleware::class, 'unload'];
        yield ExceptionHandlerMiddleware::class => [ExceptionHandlerMiddleware::class, 'unload'];
        yield JsonEncoder::class => [JsonEncoder::class, 'unload'];
        yield JsonResponseFactory::class => [JsonResponseFactory::class, 'unload'];
        yield NoContentRequestHandler::class => [NoContentRequestHandler::class, 'unload'];
        yield NotFoundRequestHandler::class => [NotFoundRequestHandler::class, 'unload'];
        yield NullMiddleware::class => [NullMiddleware::class, 'unload'];
        yield OkRequestHandler::class => [OkRequestHandler::class, 'unload'];
        yield PipelineRequestHandler::class => [PipelineRequestHandler::class, 'unload'];
        yield ResponseEmitter::class => [ResponseEmitter::class, 'unload'];
        yield ResponseExiter::class => [ResponseExiter::class, 'unload'];
        yield RouteRequestHandler::class => [RouteRequestHandler::class, 'unload'];
        yield RequestHandlerInterface::class => [RouteRequestHandler::class, 'unload'];
        yield RouteMatcher::class => [RouteMatcher::class, 'unload'];
        yield StreamWriter::class => [StreamWriter::class, 'unload'];
        yield WithRequestCookiesMiddleware::class => [WithRequestCookiesMiddleware::class, 'unload'];
        yield WithRequestHeadersMiddleware::class => [WithRequestHeadersMiddleware::class, 'unload'];
        yield WithRequestPayloadMiddleware::class => [WithRequestPayloadMiddleware::class, 'unload'];
        yield WithRequestQueryMiddleware::class => [WithRequestQueryMiddleware::class, 'unload'];
    }
}
