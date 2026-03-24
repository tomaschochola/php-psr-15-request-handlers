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
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RequestParseBodyException;

use function assert;
use function is_array;
use function is_int;
use function is_string;
use function request_parse_body;

use const UPLOAD_ERR_OK;

/**
 * @no-named-arguments
 */
readonly class WithRequestFormMiddleware implements MiddlewareInterface
{
    private readonly StreamFactoryInterface $streamFactory;

    private readonly UploadedFileFactoryInterface $uploadedFileFactory;

    public function __construct(StreamFactoryInterface $streamFactory, UploadedFileFactoryInterface $uploadedFileFactory)
    {
        $this->streamFactory = $streamFactory;
        $this->uploadedFileFactory = $uploadedFileFactory;
    }

    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        $streamFactory = $container->get(StreamFactoryInterface::class);
        $uploadedFileFactory = $container->get(UploadedFileFactoryInterface::class);

        assert($streamFactory instanceof StreamFactoryInterface);
        assert($uploadedFileFactory instanceof UploadedFileFactoryInterface);

        return new self($streamFactory, $uploadedFileFactory);
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
            $request = $request->withUploadedFiles($this->uploadedFiles($files));
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
     * @return array<mixed, mixed>
     */
    #[NoDiscard]
    private function uploadedFiles(array $files): array
    {
        $result = [];

        foreach ($files as $key => $value) {
            assert(is_array($value));

            if (!isset($value['tmp_name']) || is_array($value['tmp_name'])) {
                $result[$key] = $this->uploadedFiles($value);
            } else {
                $tmpName = $value['tmp_name'] ?? null;
                $size = $value['size'] ?? null;
                $error = $value['error'] ?? null;
                $clientFilename = $value['name'] ?? null;
                $clientMediaType = $value['type'] ?? null;

                assert(is_string($tmpName));
                assert(is_int($size));
                assert(is_int($error));
                assert($clientFilename === null || is_string($clientFilename));
                assert($clientMediaType === null || is_string($clientMediaType));

                if ($error === UPLOAD_ERR_OK) {
                    $stream = $this->streamFactory->createStreamFromFile($tmpName, 'rb');
                } else {
                    $stream = $this->streamFactory->createStream();
                }

                $result[$key] = $this->uploadedFileFactory->createUploadedFile($stream, $size, $error, $clientFilename, $clientMediaType);
            }
        }

        return $result;
    }
}
