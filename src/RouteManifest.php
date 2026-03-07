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

use AppendIterator;
use ArrayIterator;
use Iterator;
use IteratorAggregate;
use NoDiscard;
use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TomasChochola\Psr\Container\MixedCargo;
use Traversable;

use function array_replace_recursive;
use function array_reverse;
use function explode;
use function iterator_to_array;
use function str_contains;

/**
 * @no-named-arguments
 *
 * @implements IteratorAggregate<mixed, mixed>
 */
readonly class RouteManifest implements IteratorAggregate
{
    /**
     * @var AppendIterator<string, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>, Iterator<string, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>>>
     */
    protected readonly AppendIterator $exact;

    /**
     * @var AppendIterator<string, array<mixed, mixed>, Iterator<string, array<mixed, mixed>>>
     */
    protected readonly AppendIterator $trie;

    public function __construct()
    {
        $this->exact = new AppendIterator();
        $this->trie = new AppendIterator();
    }

    #[NoDiscard]
    #[Override]
    public function getIterator(): Traversable
    {
        $trie = [];

        foreach ($this->trie as $method => $node) {
            $trie = array_replace_recursive($trie, [$method => $node]);
        }

        yield RouteSettingsInterface::class => new MixedCargo(new RouteSettings(iterator_to_array($this->exact), $trie));
    }

    /**
     * @param iterable<mixed, string> $methods
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $pipeline
     */
    public function route(iterable $methods, string $path, iterable $pipeline): void
    {
        foreach ($methods as $method) {
            if (str_contains($path, '?')) {
                $this->trie($method, $path, $pipeline);
            } else {
                $this->exact($method, $path, $pipeline);
            }
        }
    }

    /**
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $pipeline
     */
    protected function exact(string $method, string $path, iterable $pipeline): void
    {
        $this->exact->append(new ArrayIterator(["{$method} {$path}" => iterator_to_array($pipeline, false)]));
    }

    /**
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $pipeline
     */
    protected function trie(string $method, string $path, iterable $pipeline): void
    {
        $trie = ['#' => iterator_to_array($pipeline, false)];

        foreach (array_reverse(explode('/', $path)) as $chunk) {
            if ($chunk === '') {
                continue;
            }

            $trie = [$chunk => $trie];
        }

        $this->trie->append(new ArrayIterator([$method => $trie]));
    }
}
