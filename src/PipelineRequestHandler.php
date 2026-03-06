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

use EmptyIterator;
use Iterator;
use LogicException;
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
readonly class PipelineRequestHandler implements RequestHandlerInterface
{
    protected readonly ContainerInterface $container;

    protected readonly Iterator $pipeline;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->pipeline = new EmptyIterator();
    }

    #[NoDiscard]
    public static function unload(ContainerInterface $container): self
    {
        return new self($container);
    }

    #[NoDiscard]
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->pipeline->valid()) {
            throw new LogicException('never');
        }

        $current = $this->pipeline->current();

        $this->pipeline->next();

        $instance = $this->container->get($current);

        if ($instance instanceof MiddlewareInterface) {
            return $instance->process($request, $this);
        }

        if ($instance instanceof RequestHandlerInterface) {
            return $instance->handle($request);
        }

        throw new LogicException('never');
    }

    public function withPipeline(Iterator $pipeline): static
    {
        return clone ($this, [
            'pipeline' => $pipeline,
        ]);
    }
}
