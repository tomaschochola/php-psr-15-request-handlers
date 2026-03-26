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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function explode;
use function is_array;
use function is_iterable;
use function rawurldecode;

/**
 * @no-named-arguments
 */
readonly class RouteMatcher implements RouteMatcherInterface
{
    private readonly RouteSettingsInterface $registry;

    public function __construct(RouteSettingsInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @return object{pipeline: iterable<mixed, class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>, params: list<string>}
     */
    #[NoDiscard]
    #[Override]
    public function match(ServerRequestInterface $request): object
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $exact = $this->registry->exact["{$method} {$path}"] ?? null;

        if (is_iterable($exact)) {
            return (object) [
                'pipeline' => $exact,
                'params' => [],
            ];
        }

        $trie = $this->registry->trie[$method] ?? null;

        if (!is_array($trie)) {
            return (object) [
                'pipeline' => [],
                'params' => [],
            ];
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
                $params[] = rawurldecode($chunk);

                continue;
            }

            return (object) [
                'pipeline' => [],
                'params' => [],
            ];
        }

        $pipeline = $node['#'] ?? null;

        if (is_iterable($pipeline)) {
            // @phpstan-ignore-next-line return.type
            return (object) [
                'pipeline' => $pipeline,
                'params' => $params,
            ];
        }

        return (object) [
            'pipeline' => [],
            'params' => [],
        ];
    }
}
