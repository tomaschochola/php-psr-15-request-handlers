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
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function abs;
use function is_int;

/**
 * @no-named-arguments
 */
readonly class NegativeCatcherMiddleware implements MiddlewareInterface
{
    private readonly ResponseFactoryInterface $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    #[NoDiscard]
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return $this->reject($e, $request, $handler);
        }
    }

    #[NoDiscard]
    protected function reject(Throwable $e, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($e instanceof HttpThrowableInterface) {
            return $this->responseFactory->createResponse($e->status);
        }

        $code = $e->getCode();

        if (!is_int($code)) {
            return $this->responseFactory->createResponse(500);
        }

        if ($code < 0) {
            $status = abs($code);

            if ($status >= 400 && $status <= 599) {
                return $this->responseFactory->createResponse($status);
            }
        }

        throw $e;
    }
}
