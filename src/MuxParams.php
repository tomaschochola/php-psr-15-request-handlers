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
use LogicException;
use Override;
use Traversable;

use function is_int;

/**
 * @no-named-arguments
 *
 * @implements IteratorAggregate<int, string>
 */
readonly class MuxParams implements IteratorAggregate, MuxParamsInterface
{
    /**
     * @var list<string>
     */
    protected readonly array $params;

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
        if (!is_int($offset)) {
            return false;
        }

        return isset($this->params[$offset]);
    }

    #[Override]
    public function offsetGet(mixed $offset): mixed
    {
        if (!is_int($offset)) {
            return null;
        }

        return $this->params[$offset] ?? null;
    }

    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new LogicException('never');
    }

    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        throw new LogicException('never');
    }
}
