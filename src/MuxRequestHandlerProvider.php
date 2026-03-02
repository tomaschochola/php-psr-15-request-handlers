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
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * @no-named-arguments
 */
readonly class MuxRequestHandlerProvider
{
    #[NoDiscard]
    public static function provide(ContainerInterface $container): RequestHandlerInterface
    {
        $deregistrator = $container->get(MuxDeregistrator::class);
        $runner = $container->get(PipelineRunner::class);
        $global = $container->get(GlobalPipelineInterface::class);

        assert($deregistrator instanceof MuxDeregistrator);
        assert($runner instanceof PipelineRunner);
        assert($global instanceof GlobalPipelineInterface);

        return new MuxRequestHandler($deregistrator, $runner, $global);
    }
}
