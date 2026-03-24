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

use Iterator;
use NoDiscard;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * @no-named-arguments
 */
readonly class PipelineResolver
{
    private readonly ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $pipeline
     *
     * @return Iterator<mixed, MiddlewareInterface|RequestHandlerInterface>
     */
    #[NoDiscard]
    public function resolve(iterable $pipeline): Iterator
    {
        foreach ($pipeline as $class) {
            $resolved = $this->container->get($class);

            assert($resolved instanceof MiddlewareInterface || $resolved instanceof RequestHandlerInterface);

            yield $resolved;
        }
    }
}
