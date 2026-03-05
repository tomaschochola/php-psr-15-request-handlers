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
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TomasChochola\Psr\Container\CallableResolver;
use TomasChochola\Psr\Container\RegistrarInterface;
use Traversable;

/**
 * @no-named-arguments
 */
readonly class RequestHandlersRegistrar implements RegistrarInterface
{
    #[NoDiscard]
    #[Override]
    public function getIterator(): Traversable
    {
        yield ErrorHandlerMiddleware::class => new CallableResolver([ErrorHandlerMiddleware::class, 'provide']);

        yield ExceptionHandlerMiddleware::class => new CallableResolver([ExceptionHandlerMiddleware::class, 'provide']);

        yield JsonEncoder::class => new CallableResolver([JsonEncoder::class, 'provide']);

        yield JsonResponseFactory::class => new CallableResolver([JsonResponseFactory::class, 'provide']);

        yield NoContentRequestHandler::class => new CallableResolver([NoContentRequestHandler::class, 'provide']);

        yield NotFoundRequestHandler::class => new CallableResolver([NotFoundRequestHandler::class, 'provide']);

        yield NullMiddleware::class => new CallableResolver([NullMiddleware::class, 'provide']);

        yield OkRequestHandler::class => new CallableResolver([OkRequestHandler::class, 'provide']);

        yield ResponseEmitter::class => new CallableResolver([ResponseEmitter::class, 'provide']);

        yield ResponseExiter::class => new CallableResolver([ResponseExiter::class, 'provide']);

        yield MuxRequestHandler::class => new CallableResolver([MuxRequestHandler::class, 'provide']);

        yield StreamWriter::class => new CallableResolver([StreamWriter::class, 'provide']);

        yield WithRequestCookiesMiddleware::class => new CallableResolver([WithRequestCookiesMiddleware::class, 'provide']);

        yield WithRequestHeadersMiddleware::class => new CallableResolver([WithRequestHeadersMiddleware::class, 'provide']);

        yield WithRequestPayloadMiddleware::class => new CallableResolver([WithRequestPayloadMiddleware::class, 'provide']);

        yield WithRequestQueryMiddleware::class => new CallableResolver([WithRequestQueryMiddleware::class, 'provide']);

        yield RequestHandlerInterface::class => new CallableResolver([MuxRequestHandler::class, 'provide']);

        yield MiddlewareInterface::class => new CallableResolver([GlobalPipeline::class, 'provide']);

        yield PipelineRequestHandler::class => new CallableResolver([PipelineRequestHandler::class, 'provide']);

        yield MuxDeregistrator::class => new CallableResolver([MuxDeregistrator::class, 'provide']);

        yield GlobalPipelineInterface::class => new CallableResolver([GlobalPipeline::class, 'provide']);
    }
}
