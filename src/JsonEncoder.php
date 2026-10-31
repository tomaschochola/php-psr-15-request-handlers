<?php

declare(strict_types=1);

namespace TomasChochola\Psr\Http\RequestHandlers;

use NoDiscard;
use UnexpectedValueException;

use function is_string;
use function json_encode;

use const JSON_INVALID_UTF8_SUBSTITUTE;
use const JSON_PRESERVE_ZERO_FRACTION;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;

/**
 * @no-named-arguments
 */
readonly class JsonEncoder
{
    #[NoDiscard()]
    public function encode(mixed $data): string
    {
        $encoded = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if (!is_string($encoded)) {
            throw new UnexpectedValueException('json_encode');
        }

        return $encoded;
    }
}
