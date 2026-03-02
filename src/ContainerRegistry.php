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
readonly class ContainerRegistry implements RegistrarInterface
{
    #[NoDiscard]
    #[Override]
    public function getIterator(): Traversable
    {
        yield ErrorHandlerMiddleware::class => new CallableResolver([ErrorHandlerMiddlewareProvider::class, 'provide']);

        yield ExceptionHandlerMiddleware::class => new CallableResolver([ExceptionHandlerMiddlewareProvider::class, 'provide']);

        yield JsonEncoder::class => new CallableResolver([JsonEncoderProvider::class, 'provide']);

        yield JsonResponseFactory::class => new CallableResolver([JsonResponseFactoryProvider::class, 'provide']);

        yield NoContentRequestHandler::class => new CallableResolver([NoContentRequestHandlerProvider::class, 'provide']);

        yield NotFoundRequestHandler::class => new CallableResolver([NotFoundRequestHandlerProvider::class, 'provide']);

        yield NullMiddleware::class => new CallableResolver([NullMiddlewareProvider::class, 'provide']);

        yield OkRequestHandler::class => new CallableResolver([OkRequestHandlerProvider::class, 'provide']);

        yield ResponseEmitter::class => new CallableResolver([ResponseEmitterProvider::class, 'provide']);

        yield ResponseExiter::class => new CallableResolver([ResponseExiterProvider::class, 'provide']);

        yield MuxRequestHandler::class => new CallableResolver([MuxRequestHandlerProvider::class, 'provide']);

        yield StreamWriter::class => new CallableResolver([StreamWriterProvider::class, 'provide']);

        yield WithRequestCookiesMiddleware::class => new CallableResolver([WithRequestCookiesMiddlewareProvider::class, 'provide']);

        yield WithRequestHeadersMiddleware::class => new CallableResolver([WithRequestHeadersMiddlewareProvider::class, 'provide']);

        yield WithRequestPayloadMiddleware::class => new CallableResolver([WithRequestPayloadMiddlewareProvider::class, 'provide']);

        yield WithRequestQueryMiddleware::class => new CallableResolver([WithRequestQueryMiddlewareProvider::class, 'provide']);

        yield RequestHandlerInterface::class => new CallableResolver([MuxRequestHandlerProvider::class, 'provide']);

        yield MiddlewareInterface::class => new CallableResolver([GlobalPipelineProvider::class, 'provide']);

        yield PipelineRunner::class => new CallableResolver([PipelineRunnerProvider::class, 'provide']);

        yield MuxDeregistrator::class => new CallableResolver([MuxDeregistratorProvider::class, 'provide']);

        yield GlobalPipelineInterface::class => new CallableResolver([GlobalPipelineProvider::class, 'provide']);
    }
}
