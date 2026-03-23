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

use function assert;

/**
 * @no-named-arguments
 */
readonly class RouteRequestHandler implements RequestHandlerInterface
{
    private readonly AfterPipeline $after;

    private readonly BeforePipeline $before;

    private readonly RouteMatcher $matcher;

    private readonly PipelineResolver $resolver;

    public function __construct(RouteMatcher $matcher, PipelineResolver $resolver, BeforePipeline $before, AfterPipeline $after)
    {
        $this->matcher = $matcher;
        $this->resolver = $resolver;
        $this->before = $before;
        $this->after = $after;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $after = $container->get(AfterPipeline::class);
        $before = $container->get(BeforePipeline::class);
        $matcher = $container->get(RouteMatcher::class);
        $resolver = $container->get(PipelineResolver::class);

        assert($after instanceof AfterPipeline);
        assert($before instanceof BeforePipeline);
        assert($matcher instanceof RouteMatcher);
        assert($resolver instanceof PipelineResolver);

        return new self($matcher, $resolver, $before, $after);
    }

    #[NoDiscard]
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $match = $this->matcher->match($request);

        return (new PipelineRunner($this->resolver->resolve($this->pipeline($request, $match->pipeline))))->handle($request->withAttribute(RouteParams::class, new RouteParams($match->params)));
    }

    /**
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $pipeline
     *
     * @return iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    #[NoDiscard]
    private function pipeline(ServerRequestInterface $request, iterable $pipeline): iterable
    {
        yield from $this->before->pipeline($request);

        yield from $pipeline;

        yield from $this->after->pipeline($request);
    }
}
