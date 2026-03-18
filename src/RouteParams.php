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
use IteratorAggregate;
use Override;
use Traversable;

use function array_key_exists;

/**
 * @no-named-arguments
 *
 * @implements IteratorAggregate<int, string>
 * @implements ArrayAccess<int, string>
 */
readonly class RouteParams implements IteratorAggregate, \ArrayAccess
{
    /**
     * @var list<string>
     */
    public readonly array $params;

    /**
     * @param list<string> $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->params);
    }

    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->params);
    }

    #[Override]
    public function offsetGet(mixed $offset): string
    {
        return $this->params[$offset];
    }

    #[Override]
    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new \LogicException('RouteParams is readonly');
    }

    #[Override]
    public function offsetUnset(mixed $offset): never
    {
        throw new \LogicException('RouteParams is readonly');
    }
}
