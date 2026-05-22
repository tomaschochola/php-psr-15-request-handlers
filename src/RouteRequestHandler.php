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

use Iterator;
use NoDiscard;
use Override;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;
use function explode;
use function is_array;
use function is_iterable;
use function is_string;
use function rawurldecode;

/**
 * @no-named-arguments
 */
readonly class RouteRequestHandler implements RequestHandlerInterface
{
    private ContainerInterface $container;

    private RouteSettingsInterface $registry;

    public function __construct(RouteSettingsInterface $registry, ContainerInterface $container)
    {
        $this->registry = $registry;
        $this->container = $container;
    }

    #[NoDiscard()]
    #[Override()]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        [$pipeline, $params] = $this->match($request);

        return (new PipelineRunner($this->resolve($pipeline)))->handle($request->withAttribute(RouteParams::class, new RouteParams($params)));
    }

    /**
     * @return array{0: iterable<mixed, mixed>, 1: list<string>}
     */
    #[NoDiscard()]
    private function match(ServerRequestInterface $request): array
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $exact = $this->registry->exact["{$method} {$path}"] ?? $this->registry->exact["* {$path}"] ?? $this->registry->exact["{$method} *"] ?? $this->registry->exact['* *'] ?? null;

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
                $params[] = rawurldecode($chunk);

                continue;
            }

            return [[], []];
        }

        $pipeline = $node['#'] ?? null;

        if (is_iterable($pipeline)) {
            return [$pipeline, $params];
        }

        return [[], []];
    }

    /**
     * @param iterable<mixed, mixed> $pipeline
     *
     * @return Iterator<mixed, MiddlewareInterface|RequestHandlerInterface>
     */
    #[NoDiscard()]
    private function resolve(iterable $pipeline): Iterator
    {
        foreach ($pipeline as $class) {
            assert(is_string($class));

            $resolved = $this->container->get($class);

            assert($resolved instanceof MiddlewareInterface || $resolved instanceof RequestHandlerInterface);

            yield $resolved;
        }
    }
}
