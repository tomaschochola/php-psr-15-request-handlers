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

use ArrayIterator;
use IteratorIterator;
use NoDiscard;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Traversable;

/**
 * @no-named-arguments
 */
readonly class MuxRequestHandler implements RequestHandlerInterface
{
    protected readonly MuxDeregistrator $deregistrator;

    protected readonly GlobalPipelineInterface $global;

    protected readonly PipelineRunner $runner;

    public function __construct(MuxDeregistrator $deregistrator, PipelineRunner $runner, GlobalPipelineInterface $global)
    {
        $this->deregistrator = $deregistrator;
        $this->runner = $runner;
        $this->global = $global;
    }

    #[NoDiscard]
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->runner->handle($this->pipe($request));
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface|RequestHandlerInterface>>
     */
    #[NoDiscard]
    protected function fallback(ServerRequestInterface $request): Traversable
    {
        yield NotFoundRequestHandler::class;
    }

    /**
     * @return Traversable<mixed, class-string<MiddlewareInterface|RequestHandlerInterface>>
     */
    #[NoDiscard]
    protected function global(ServerRequestInterface $request): Traversable
    {
        return $this->global->pipeline($request);
    }

    #[NoDiscard]
    protected function pipe(ServerRequestInterface $request): ServerRequestInterface
    {
        $result = $this->deregistrator->route($request);

        $pipeline = new MuxPipeline();

        $pipeline->append(new IteratorIterator($this->global($request)));
        $pipeline->append(new ArrayIterator($result->pipeline));
        $pipeline->append(new IteratorIterator($this->fallback($request)));

        return $request->withAttribute(MuxPipelineInterface::class, $pipeline)->withAttribute(MuxParamsInterface::class, new MuxParams($result->params));
    }
}
