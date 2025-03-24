<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Tests\Unit\Factory;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use Phpro\ResourceStream\Factory\Psr7Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Psr7Stream::class)]
class Psr7StreamTest extends TestCase
{
    #[Test]
    public function it_can_open_from_psr_stream(): void
    {
        $psrStream = new Stream($internalStream = fopen('php://temp', 'r+'));
        $psrStream->write('hello');
        $psrStream->rewind();

        $stream = Psr7Stream::createFromStream($psrStream);

        self::assertNotSame($stream->unwrap(), $internalStream);
        self::assertSame('hello', $stream->getContents());
    }

    #[Test]
    public function it_can_open_from_request(): void
    {
        $request = new Request('GET', 'http://example.com', body: 'hello');

        $stream = Psr7Stream::createFromRequest($request);
        self::assertSame('hello', $stream->getContents());
    }

    #[Test]
    public function it_can_open_from_response(): void
    {
        $request = new Response(body: 'hello');

        $stream = Psr7Stream::createFromResponse($request);
        self::assertSame('hello', $stream->getContents());
    }
}
