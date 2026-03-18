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

use function assert;
use function explode;
use function is_array;
use function is_iterable;

/**
 * @internal
 *
 * @no-named-arguments
 */
final readonly class RouteMatcher
{
    private readonly RouteSettings $registry;

    public function __construct(RouteSettings $registry)
    {
        $this->registry = $registry;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $registry = $container->get(RouteSettings::class);

        assert($registry instanceof RouteSettings);

        return new self($registry);
    }

    #[NoDiscard]
    public function match(ServerRequestInterface $request): RouteMatch
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $exact = $this->registry->exact["{$method} {$path}"] ?? null;

        if (is_iterable($exact)) {
            return new RouteMatch($exact, new RouteParams([]));
        }

        $trie = $this->registry->trie[$method] ?? null;

        if (!is_array($trie)) {
            return new RouteMatch([], new RouteParams([]));
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

            return new RouteMatch([], new RouteParams([]));
        }

        $pipeline = $node['#'] ?? null;

        if (is_iterable($pipeline)) {
            // @phpstan-ignore-next-line argument.type
            return new RouteMatch($pipeline, new RouteParams($params));
        }

        return new RouteMatch([], new RouteParams([]));
    }
}
