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

use ArrayObject;
use Iterator;
use NoDiscard;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Traversable;

/**
 * @no-named-arguments
 */
readonly class RouteRequestHandler implements RequestHandlerInterface
{
    private readonly RouteMatcher $deregistrator;

    private readonly PipelineRequestHandler $runner;

    public function __construct(RouteMatcher $deregistrator, PipelineRequestHandler $runner)
    {
        $this->deregistrator = $deregistrator;
        $this->runner = $runner;
    }

    #[NoDiscard]
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        [$pipeline, $params] = $this->deregistrator->route($request);

        return $this->runner->withPipeline($this->pipeline($request, $pipeline))->handle($request->withAttribute(ArrayObject::class, new ArrayObject($params)));
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private function after(ServerRequestInterface $request): Traversable
    {
        yield NotFoundRequestHandler::class;
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private function before(ServerRequestInterface $request): Traversable
    {
        yield ExceptionHandlerMiddleware::class;

        yield ErrorHandlerMiddleware::class;
    }

    /**
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $match
     *
     * @return Iterator<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private function pipeline(ServerRequestInterface $request, iterable $match): Iterator
    {
        yield from $this->before($request);

        yield from $match;

        yield from $this->after($request);
    }
}
