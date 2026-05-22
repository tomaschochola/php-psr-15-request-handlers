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
use LogicException;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @no-named-arguments
 */
readonly class PipelineRunner implements RequestHandlerInterface
{
    /**
     * @var Iterator<mixed, MiddlewareInterface|RequestHandlerInterface>
     */
    private Iterator $pipeline;

    /**
     * @param Iterator<mixed, MiddlewareInterface|RequestHandlerInterface> $pipeline
     */
    public function __construct(Iterator $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    #[Override()]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->pipeline->valid()) {
            throw new LogicException('never');
        }

        $current = $this->pipeline->current();

        $this->pipeline->next();

        if ($current instanceof MiddlewareInterface) {
            return $current->process($request, $this);
        }

        if ($current instanceof RequestHandlerInterface) {
            return $current->handle($request);
        }

        throw new LogicException('never');
    }
}
