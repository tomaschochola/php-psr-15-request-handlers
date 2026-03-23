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
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RequestParseBodyException;
use TomasChochola\Psr\Http\Message\HttpUploadedFile;

use function is_array;
use function request_parse_body;

/**
 * @no-named-arguments
 */
readonly class WithRequestFormMiddleware implements MiddlewareInterface
{
    public function __construct() {}

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        return new self();
    }

    #[NoDiscard]
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $parsed = request_parse_body();
        } catch (RequestParseBodyException) {
            return $this->reject($request, $handler);
        }

        $body = $parsed[0] ?? [];
        $files = $parsed[1] ?? [];

        if ($body !== []) {
            $request = $request->withParsedBody($body);
        }

        if ($files !== []) {
            $request = $request->withUploadedFiles(self::uploadedFiles($files));
        }

        return $handler->handle($request);
    }

    #[NoDiscard]
    protected function reject(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }

    /**
     * @param array<mixed, mixed> $files
     * @return array<mixed, UploadedFileInterface|array<mixed, UploadedFileInterface|array<mixed, UploadedFileInterface|array<mixed, UploadedFileInterface|array<mixed, UploadedFileInterface>>>>>
     */
    #[NoDiscard]
    private static function uploadedFiles(array $files): array
    {
        $result = [];

        foreach ($files as $key => $value) {
            if (is_array($value) && isset($value['tmp_name'], $value['size'], $value['error']) && ! is_array($value['tmp_name'])) {
                $result[$key] = new HttpUploadedFile($value['tmp_name'], $value['size'], $value['error'], $value['name'] ?? null, $value['type'] ?? null);
            } else {
                $result[$key] = self::uploadedFiles($value);
            }
        }

        return $result;
    }
}
