<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use Psr\Http\Message\ResponseInterface;

/**
 * @no-named-arguments
 */
interface ResponseEmitterInterface
{
    public function emit(ResponseInterface $response): void;
}
