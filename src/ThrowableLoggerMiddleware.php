<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @no-named-arguments
 */
readonly class ThrowableLoggerMiddleware implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[NoDiscard()]
    #[Override()]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            $this->log($e, $request, $handler);

            throw $e;
        }
    }

    protected function log(Throwable $e, ServerRequestInterface $request, RequestHandlerInterface $handler): void
    {
        $this->logger->error($e->getMessage(), [
            'exception' => $e,
        ]);
    }
}
