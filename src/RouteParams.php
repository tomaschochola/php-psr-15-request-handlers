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

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use LogicException;
use Override;
use Traversable;

use function is_int;

/**
 * @no-named-arguments
 *
 * @implements IteratorAggregate<int, string>
 * @implements ArrayAccess<int, string>
 */
readonly class RouteParams implements ArrayAccess, IteratorAggregate
{
    /**
     * @var array<int, string>
     */
    private readonly array $params;

    /**
     * @param array<int, string> $params
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
        if (!is_int($offset)) {
            throw new InvalidArgumentException('$offset');
        }

        return isset($this->params[$offset]);
    }

    #[Override]
    public function offsetGet(mixed $offset): string
    {
        if (!is_int($offset) || !isset($this->params[$offset])) {
            throw new InvalidArgumentException('$offset');
        }

        return $this->params[$offset];
    }

    #[Override]
    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new LogicException('never');
    }

    #[Override]
    public function offsetUnset(mixed $offset): never
    {
        throw new LogicException('never');
    }
}
