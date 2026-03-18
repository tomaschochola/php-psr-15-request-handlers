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

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @internal
 *
 * @no-named-arguments
 */
final readonly class RouteSettings
{
    /**
     * @var array<mixed, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>>
     */
    public readonly array $exact;

    /**
     * @var array<mixed, mixed>
     */
    public readonly array $trie;

    /**
     * @param array<mixed, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>> $exact
     * @param array<mixed, mixed> $trie
     */
    public function __construct(array $exact, array $trie)
    {
        $this->exact = $exact;
        $this->trie = $trie;
    }
}
