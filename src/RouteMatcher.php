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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function explode;
use function is_array;
use function is_iterable;

/**
 * @no-named-arguments
 */
readonly class RouteMatcher
{
    protected readonly RouteSettingsInterface $registry;

    public function __construct(RouteSettingsInterface $registry)
    {
        $this->registry = $registry;
    }

    #[NoDiscard]
    public static function unload(ContainerInterface $container): self
    {
        $registry = $container->get(RouteSettingsInterface::class);

        assert($registry instanceof RouteSettingsInterface);

        return new self($registry);
    }

    /**
     * @return array{0: iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>, 1: list<string>}
     */
    #[NoDiscard]
    public function route(ServerRequestInterface $request): array
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $exact = $this->registry->exact["{$method} {$path}"] ?? null;

        if (is_iterable($exact)) {
            return [$exact, []];
        }

        $trie = $this->registry->trie[$method] ?? null;

        if (!is_array($trie)) {
            return [[], []];
        }

        $node = $trie;
        $chunks = explode('/', $path);
        $params = [];

        foreach ($chunks as $chunk) {
            if ($chunk === '') {
                continue;
            }

            $try = $node[$chunk] ?? null;

            if (is_array($try)) {
                $node = $try;

                continue;
            }

            $try = $node['?'] ?? null;

            if (is_array($try)) {
                $node = $try;
                $params[] = $chunk;

                continue;
            }

            return [[], []];
        }

        $pipeline = $node['#'] ?? null;

        if (is_iterable($pipeline)) {
            /** @phpstan-ignore-next-line return.type */
            return [$pipeline, $params];
        }

        return [[], []];
    }
}
