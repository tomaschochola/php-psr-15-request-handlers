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
use TomasChochola\Psr\Container\CallableCargo;
use Traversable;

/**
 * @no-named-arguments
 *
 * @implements IteratorAggregate<mixed, mixed>
 */
readonly class RequestHandlersManifest implements IteratorAggregate
{
    #[NoDiscard]
    #[Override]
    public function getIterator(): Traversable
    {
        yield ErrorHandlerMiddleware::class => new CallableCargo([ErrorHandlerMiddleware::class, 'unload']);

        yield ExceptionHandlerMiddleware::class => new CallableCargo([ExceptionHandlerMiddleware::class, 'unload']);

        yield JsonEncoder::class => new CallableCargo([JsonEncoder::class, 'unload']);

        yield JsonResponseForge::class => new CallableCargo([JsonResponseForge::class, 'unload']);

        yield NoContentRequestHandler::class => new CallableCargo([NoContentRequestHandler::class, 'unload']);

        yield NotFoundRequestHandler::class => new CallableCargo([NotFoundRequestHandler::class, 'unload']);

        yield NullMiddleware::class => new CallableCargo([NullMiddleware::class, 'unload']);

        yield OkRequestHandler::class => new CallableCargo([OkRequestHandler::class, 'unload']);

        yield PipelineRequestHandler::class => new CallableCargo([PipelineRequestHandler::class, 'unload']);

        yield ResponseEmitter::class => new CallableCargo([ResponseEmitter::class, 'unload']);

        yield ResponseExiter::class => new CallableCargo([ResponseExiter::class, 'unload']);

        yield RouteRequestHandler::class => new CallableCargo([RouteRequestHandler::class, 'unload']);

        yield RequestHandlerInterface::class => new CallableCargo([RouteRequestHandler::class, 'unload']);

        yield RouteMatcher::class => new CallableCargo([RouteMatcher::class, 'unload']);

        yield StreamWriter::class => new CallableCargo([StreamWriter::class, 'unload']);

        yield WithRequestCookiesMiddleware::class => new CallableCargo([WithRequestCookiesMiddleware::class, 'unload']);

        yield WithRequestHeadersMiddleware::class => new CallableCargo([WithRequestHeadersMiddleware::class, 'unload']);

        yield WithRequestPayloadMiddleware::class => new CallableCargo([WithRequestPayloadMiddleware::class, 'unload']);

        yield WithRequestQueryMiddleware::class => new CallableCargo([WithRequestQueryMiddleware::class, 'unload']);
    }
}
