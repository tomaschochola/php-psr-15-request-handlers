<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use Override;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @no-named-arguments
 */
readonly class RouteSettings implements RouteSettingsInterface
{
    /**
     * @var array<mixed, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>>
     */
    #[Override()]
    public array $exact;

    /**
     * @var array<mixed, mixed>
     */
    #[Override()]
    public array $trie;

    /**
     * @param array<mixed, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>> $exact
     * @param array<mixed, mixed> $trie
     */
    public function __construct(array $exact, array $trie)
    {
        $this->exact = $exact;
        $this->trie = $trie;
    }
}
