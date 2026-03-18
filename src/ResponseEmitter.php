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

use LogicException;
use NoDiscard;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

use function fclose;
use function fopen;
use function header;
use function headers_sent;
use function http_response_code;
use function is_resource;
use function ob_get_level;
use function stream_copy_to_stream;

/**
 * @no-named-arguments
 */
readonly class ResponseEmitter
{
    #[NoDiscard]
    public static function inject(ContainerInterface $container): self
    {
        return new self();
    }

    public function emit(ResponseInterface $response): never
    {
        if (headers_sent()) {
            throw new LogicException('headers_sent');
        }

        if (ob_get_level() > 0) {
            throw new LogicException('ob_get_level');
        }

        foreach ($response->getHeaders() as $name => $values) {
            $replace = true;

            foreach ($values as $value) {
                header("{$name}: {$value}", $replace);

                $replace = false;
            }
        }

        http_response_code($response->getStatusCode());

        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        $to = fopen('php://output', 'w');

        if (!is_resource($to)) {
            throw new UnexpectedValueException('fopen');
        }

        $from = $body->detach();

        if (!is_resource($from)) {
            throw new UnexpectedValueException($body::class . '->detach');
        }

        stream_copy_to_stream($from, $to);

        $closed = fclose($to);

        if ($closed !== true) {
            throw new UnexpectedValueException('fclose');
        }

        $closed = fclose($from);

        if ($closed !== true) {
            throw new UnexpectedValueException('fclose');
        }

        exit(0);
    }
}
