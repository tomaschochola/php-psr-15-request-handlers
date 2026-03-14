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
readonly class RequestHandlersManifest implements IteratorAggregate
{
    #[NoDiscard]
    #[Override]
    public function getIterator(): Traversable
    {
        yield ErrorHandlerMiddleware::class => [ErrorHandlerMiddlewareAssembler::class, 'assemble'];
        yield ExceptionHandlerMiddleware::class => [ExceptionHandlerMiddlewareAssembler::class, 'assemble'];
        yield JsonWriter::class => [JsonWriterAssembler::class, 'assemble'];
        yield JsonResponseFactory::class => [JsonResponseFactoryAssembler::class, 'assemble'];
        yield NoContentRequestHandler::class => [NoContentRequestHandlerAssembler::class, 'assemble'];
        yield NotFoundRequestHandler::class => [NotFoundRequestHandlerAssembler::class, 'assemble'];
        yield NullMiddleware::class => [NullMiddlewareAssembler::class, 'assemble'];
        yield OkRequestHandler::class => [OkRequestHandlerAssembler::class, 'assemble'];
        yield PipelineRequestHandler::class => [PipelineRequestHandlerAssembler::class, 'assemble'];
        yield ResponseEmitter::class => [ResponseEmitterAssembler::class, 'assemble'];
        yield ResponseExiter::class => [ResponseExiterAssembler::class, 'assemble'];
        yield RouteRequestHandler::class => [RouteRequestHandlerAssembler::class, 'assemble'];
        yield RequestHandlerInterface::class => [RouteRequestHandlerAssembler::class, 'assemble'];
        yield RouteMatcher::class => [RouteMatcherAssembler::class, 'assemble'];
        yield StreamWriter::class => [StreamWriterAssembler::class, 'assemble'];
        yield WithRequestCookiesMiddleware::class => [WithRequestCookiesMiddlewareAssembler::class, 'assemble'];
        yield WithRequestHeadersMiddleware::class => [WithRequestHeadersMiddlewareAssembler::class, 'assemble'];
        yield WithRequestPayloadMiddleware::class => [WithRequestPayloadMiddlewareAssembler::class, 'assemble'];
        yield WithRequestQueryMiddleware::class => [WithRequestQueryMiddlewareAssembler::class, 'assemble'];
    }
}
