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

/**
 * @no-named-arguments
 */
readonly class MuxDeregistrator
{
    protected readonly MuxRegistryInterface $registry;

    public function __construct(MuxRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    #[NoDiscard]
    public static function provide(ContainerInterface $container): self
    {
        $registry = $container->get(MuxRegistryInterface::class);

        assert($registry instanceof MuxRegistryInterface);

        return new self($registry);
    }

    #[NoDiscard]
    public function route(ServerRequestInterface $request): MuxResultInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();

        $exact = $this->registry->exact["{$method} {$path}"] ?? null;

        if (is_array($exact)) {
            return new MuxResult($exact, []);
        }

        $trie = $this->registry->trie[$method] ?? null;

        if (!is_array($trie)) {
            return new MuxResult([], []);
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

            return new MuxResult([], []);
        }

        $pipeline = $node['#'] ?? null;

        if (is_array($pipeline)) {
            /** @phpstan-ignore-next-line argument.type */
            return new MuxResult($pipeline, $params);
        }

        return new MuxResult([], []);
    }
}
