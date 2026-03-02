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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Traversable;

/**
 * @no-named-arguments
 */
interface PipelineCollectionInterface
{
    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface|RequestHandlerInterface>>
     */
    #[NoDiscard]
    public function pipeline(RequestInterface $request): Traversable;
}
