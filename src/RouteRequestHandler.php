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

use ArrayIterator;
use ArrayObject;
use Iterator;
use IteratorIterator;
use NoDiscard;
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Traversable;

use function assert;

/**
 * @no-named-arguments
 */
readonly class RouteRequestHandler implements RequestHandlerInterface
{
    protected readonly RouteMatcher $deregistrator;

    protected readonly PipelineRequestHandler $runner;

    public function __construct(RouteMatcher $deregistrator, PipelineRequestHandler $runner)
    {
        $this->deregistrator = $deregistrator;
        $this->runner = $runner;
    }

    #[NoDiscard]
    public static function unload(ContainerInterface $container): self
    {
        $deregistrator = $container->get(RouteMatcher::class);
        $runner = $container->get(PipelineRequestHandler::class);

        assert($deregistrator instanceof RouteMatcher);
        assert($runner instanceof PipelineRequestHandler);

        return new self($deregistrator, $runner);
    }

    #[NoDiscard]
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        [$pipeline, $params] = $this->deregistrator->route($request);

        return $this->runner->withPipeline($this->pipeline($request, $pipeline))->handle($request->withAttribute(ArrayObject::class, new ArrayObject($params)));
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface|RequestHandlerInterface>>
     */
    #[NoDiscard]
    protected function after(ServerRequestInterface $request): Traversable
    {
        yield NotFoundRequestHandler::class;
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface|RequestHandlerInterface>>
     */
    #[NoDiscard]
    protected function before(ServerRequestInterface $request): Traversable
    {
        yield ExceptionHandlerMiddleware::class;

        yield ErrorHandlerMiddleware::class;
    }

    #[NoDiscard]
    protected function pipeline(ServerRequestInterface $request, iterable $match): Iterator
    {
        yield from $this->before($request);

        yield from $match;

        yield from $this->after($request);
    }
}
