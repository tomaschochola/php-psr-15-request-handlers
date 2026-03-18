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

/**
 * @internal
 *
 * @no-named-arguments
 */
readonly class RouteMatch
{
    /**
     * @var iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>
     */
    public readonly iterable $pipeline;

    public readonly RouteParams $params;

    /**
     * @param iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $pipeline
     */
    public function __construct(iterable $pipeline, RouteParams $params)
    {
        $this->pipeline = $pipeline;
        $this->params = $params;
    }
}
