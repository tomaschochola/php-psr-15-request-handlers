<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @no-named-arguments
 */
interface RouteSettingsInterface
{
    /**
     * @var array<mixed, list<class-string<MiddlewareInterface>|class-string<RequestHandlerInterface>>>
     */
    public array $exact { get; }

    /**
     * @var array<mixed, mixed>
     */
    public array $trie { get; }
}
