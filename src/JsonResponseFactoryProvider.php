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
use TomasChochola\Psr\Container\ProviderInterface;

use function assert;

/**
 * @no-named-arguments
 */
readonly class JsonResponseFactoryProvider implements ProviderInterface
{
    #[NoDiscard]
    #[Override]
    public static function provide(ContainerInterface $container): JsonResponseFactory
    {
        $jsonEncoder = $container->get(JsonEncoder::class);
        $streamWriter = $container->get(StreamWriter::class);
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        assert($jsonEncoder instanceof JsonEncoder);
        assert($streamWriter instanceof StreamWriter);
        assert($responseFactory instanceof ResponseFactoryInterface);

        return new JsonResponseFactory($jsonEncoder, $streamWriter, $responseFactory);
    }
}
