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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use TomasChochola\Psr\Container\ProviderInterface;

use function assert;

/**
 * @no-named-arguments
 */
readonly class WithRequestPayloadMiddlewareProvider implements ProviderInterface
{
    #[NoDiscard]
    #[Override]
    public static function provide(ContainerInterface $container): MiddlewareInterface
    {
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($responseFactory instanceof ResponseFactoryInterface);

        return new WithRequestPayloadMiddleware($responseFactory);
    }
}
