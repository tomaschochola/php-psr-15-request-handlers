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
    private readonly RouteMatcher $matcher;

    private readonly PipelineResolver $resolver;

    public function __construct(RouteMatcher $matcher, PipelineResolver $resolver)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $matcher = $container->get(RouteMatcher::class);
        $resolver = $container->get(PipelineResolver::class);

        assert($matcher instanceof RouteMatcher);
        assert($resolver instanceof PipelineResolver);

        return new self($matcher, $resolver);
    }

    #[NoDiscard]
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $match = $this->matcher->match($request);

        return (new PipelineRunner($this->resolver->resolve(self::pipeline($request, $match))))->handle($request->withAttribute(RouteParams::class, $match->params));
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private static function after(ServerRequestInterface $request): Traversable
    {
        yield NotFoundRequestHandler::class;
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private static function before(ServerRequestInterface $request): Traversable
    {
        yield ExceptionHandlerMiddleware::class;

        yield ErrorHandlerMiddleware::class;
    }

    /**
     * @return iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private static function pipeline(ServerRequestInterface $request, RouteMatch $match): iterable
    {
        yield from self::before($request);

        yield from $match->pipeline;

        yield from self::after($request);
    }
}
