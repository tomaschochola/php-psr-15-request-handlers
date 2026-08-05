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

namespace Tests;

use ArrayIterator;
use NoRewindIterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TomasChochola\Psr\Http\RequestHandlers\NullMiddleware;
use TomasChochola\Psr\Http\RequestHandlers\OkRequestHandler;
use TomasChochola\Psr\Http\RequestHandlers\RouteLoader;
use TomasChochola\Psr\Http\RequestHandlers\RouteSettings;
use TomasChochola\Psr\Http\RequestHandlers\RouteSettingsInterface;

use function iterator_to_array;

/**
 * @internal
 *
 * @no-named-arguments
 */
#[CoversClass(RouteLoader::class)]
#[CoversClass(RouteSettings::class)]
#[Small()]
final class RouteLoaderTest extends TestCase
{
    #[Test()]
    public function reusesSinglePassPipelineForEveryMethod(): void
    {
        $loader = new RouteLoader();

        /**
         * @var list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>> $classes
         */
        $classes = [
            NullMiddleware::class,
            OkRequestHandler::class,
        ];

        $pipeline = new NoRewindIterator(new ArrayIterator($classes));

        $loader->route(['GET', 'POST'], '/health', $pipeline);
        $loader->route(['GET'], '/users/?', [OkRequestHandler::class]);

        $services = iterator_to_array($loader);
        self::assertArrayHasKey(RouteSettingsInterface::class, $services);
        $settings = $services[RouteSettingsInterface::class];

        self::assertInstanceOf(RouteSettings::class, $settings);
        self::assertArrayHasKey('GET /health', $settings->exact);
        self::assertArrayHasKey('POST /health', $settings->exact);
        self::assertSame([NullMiddleware::class, OkRequestHandler::class], $settings->exact['GET /health']);
        self::assertSame([NullMiddleware::class, OkRequestHandler::class], $settings->exact['POST /health']);
        self::assertSame(['GET' => ['users' => ['?' => ['#' => [OkRequestHandler::class]]]]], $settings->trie);
    }
}
