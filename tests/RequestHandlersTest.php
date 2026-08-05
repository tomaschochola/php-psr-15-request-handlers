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

namespace Tests;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TomasChochola\Psr\Http\RequestHandlers\JsonEncoder;
use TomasChochola\Psr\Http\RequestHandlers\NullMiddleware;
use TomasChochola\Psr\Http\RequestHandlers\RouteParams;
use TomasChochola\Psr\Http\RequestHandlers\StreamWriter;
use UnexpectedValueException;

use function iterator_to_array;

/**
 * @internal
 *
 * @no-named-arguments
 */
#[CoversClass(JsonEncoder::class)]
#[CoversClass(NullMiddleware::class)]
#[CoversClass(RouteParams::class)]
#[CoversClass(StreamWriter::class)]
#[Small()]
final class RequestHandlersTest extends TestCase
{
    #[Test()]
    public function jsonEncoderPreservesDataWithoutEscapingUnicodeOrSlashes(): void
    {
        $encoded = (new JsonEncoder())->encode([
            'ratio' => 1.0,
            'url' => 'https://example.com/žluťoučký',
        ]);

        self::assertSame('{"ratio":1.0,"url":"https://example.com/žluťoučký"}', $encoded);
    }

    #[Test()]
    public function nullMiddlewareDelegatesTheOriginalRequest(): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $response = self::createStub(ResponseInterface::class);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())->method('handle')->with(self::identicalTo($request))->willReturn($response)->seal();

        self::assertSame($response, (new NullMiddleware())->process($request, $handler));
    }

    #[Test()]
    public function routeParamsExposeIntegerIndexedValuesAndRejectMissingOffsets(): void
    {
        $params = new RouteParams(['first', 'second']);

        self::assertTrue(isset($params[0]));
        self::assertFalse(isset($params[2]));
        self::assertSame('second', $params[1]);
        self::assertSame(['first', 'second'], iterator_to_array($params));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('$offset');

        (void) $params[2];
    }

    #[Test()]
    public function routeParamsRejectMutation(): void
    {
        $params = new RouteParams(['first']);

        $this->expectException(LogicException::class);

        $params[0] = 'changed';
    }

    #[Test()]
    public function routeParamsRejectRemoval(): void
    {
        $params = new RouteParams(['first']);

        $this->expectException(LogicException::class);

        unset($params[0]);
    }

    #[Test()]
    public function streamWriterAcceptsCompleteWrites(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('write')->with('payload')->willReturn(7)->seal();
        (new StreamWriter())->write($stream, 'payload');

        self::addToAssertionCount(1);
    }

    #[Test()]
    public function streamWriterRejectsPartialWrites(): void
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->expects($this->once())->method('write')->with('payload')->willReturn(6)->seal();

        $this->expectException(UnexpectedValueException::class);

        (new StreamWriter())->write($stream, 'payload');
    }
}
