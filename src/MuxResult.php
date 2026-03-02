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

use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @no-named-arguments
 */
readonly class MuxResult implements MuxResultInterface
{
    #[Override]
    public readonly array $params;

    #[Override]
    public readonly array $pipeline;

    /**
     * @param array<mixed, class-string<MiddlewareInterface|RequestHandlerInterface>> $pipeline
     * @param list<string> $params
     */
    public function __construct(array $pipeline, array $params)
    {
        $this->pipeline = $pipeline;
        $this->params = $params;
    }
}
