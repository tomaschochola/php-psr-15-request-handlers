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
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @no-named-arguments
 */
readonly class ResponseExiter extends ResponseEmitter
{
    #[NoDiscard]
    #[Override]
    public static function provide(ContainerInterface $container): self
    {
        return new self();
    }

    #[Override]
    public function emit(ResponseInterface $response): never
    {
        parent::emit($response);

        exit(0);
    }
}
