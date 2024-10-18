<?php

declare(strict_types=1);

namespace Phpro\ResourceStreamTest\Unit;

use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Factory\MemoryStream;
use Phpro\ResourceStream\ResourceStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResourceStream::class)]
class ResourceStreamTest extends TestCase
{
    #[Test]
    public function it_detects_closed_streams_during_construction(): void
    {
        $f = fopen('php://memory', 'r');
        fclose($f);

        $this->expectException(ResourceStreamException::class);
        new ResourceStream($f);
    }

    #[Test]
    public function it_can_unwrap_a_stream(): void
    {
        $f = fopen('php://memory', 'r');

        $stream = new ResourceStream($f);
        self::assertTrue($stream->isOpen());
        self::assertSame($f, $stream->unwrap());

        fclose($f);
    }

    #[Test]
    public function it_can_not_unwrap_a_closed_stream(): void
    {
        $f = fopen('php://memory', 'r');

        $stream = new ResourceStream($f);
        self::assertTrue($stream->isOpen());

        fclose($f);

        self::assertFalse($stream->isOpen());
        $this->expectException(ResourceStreamException::class);
        $stream->unwrap();
    }

    #[Test]
    public function it_can_read_and_write_from_streams(): void
    {
        $stream = MemoryStream::create();
        $result = $stream->write('hello')
            ->rewind()
            ->read();

        self::assertSame('hello', $result);
        self::assertTrue($stream->isEof());
        self::assertTrue($stream->isOpen());

        $additionalRead = $stream->read();
        self::assertSame('', $additionalRead);
    }

    #[Test]
    public function it_can_read_line_from_stream(): void
    {
        $stream = MemoryStream::create()
            ->write('hello'.PHP_EOL.'world')
            ->rewind();

        $line = $stream->readLine();

        self::assertSame('hello', $line);
    }

    #[Test]
    public function it_can_read_lines_from_stream(): void
    {
        $stream = MemoryStream::create()
            ->write('hello'.PHP_EOL.'world')
            ->rewind();

        $lines = iterator_to_array($stream->readLines());

        self::assertSame(['hello', 'world'], $lines);
    }

    #[Test]
    public function it_can_read_batches(): void
    {
        $stream = MemoryStream::create()
            ->write('hello'.PHP_EOL.'world')
            ->rewind();

        $chars = implode('', iterator_to_array($stream->readBatches(1)));

        self::assertSame('hello'.PHP_EOL.'world', $chars);
    }

    #[Test]
    public function it_can_read_content_of_stream(): void
    {
        $stream = MemoryStream::create()
            ->write($content = 'hello'.PHP_EOL.'world')
            ->rewind();

        self::assertSame($content, $stream->getContents());
    }

    #[Test]
    public function it_auto_closes_resource_on_destruct(): void
    {
        $f = fopen('php://memory', 'r');

        $stream = new ResourceStream($f);
        unset($stream);

        self::assertFalse(is_resource($f));
    }

    #[Test]
    public function it_can_keep_stream_alive(): void
    {
        $f = fopen('php://memory', 'r');

        $stream = new ResourceStream($f);
        $stream->keepAlive();
        unset($stream);

        self::assertTrue(is_resource($f));
        fclose($f);
    }

    #[Test]
    public function it_knows_the_size(): void
    {
        $stream = MemoryStream::create();
        $stream->write($content = 'hello');

        self::assertSame(mb_strlen($content), $stream->size());
    }

    #[Test]
    public function it_knows_the_uri(): void
    {
        $stream = MemoryStream::create();

        self::assertSame('php://memory', $stream->uri());
    }

    #[Test]
    public function it_can_copy_from(): void
    {
        $stream1 = MemoryStream::create();
        $stream2 = MemoryStream::create()->write('hello')->rewind();

        $stream1->copyFrom($stream2)->rewind();

        self::assertSame('hello', $stream1->getContents());
    }

    #[Test]
    public function it_can_copy_to(): void
    {
        $stream1 = MemoryStream::create();
        $stream2 = MemoryStream::create()->write('hello')->rewind();

        $stream2->copyTo($stream1)->rewind();

        self::assertSame('hello', $stream1->getContents());
    }

    #[Test]
    public function it_doesnt_fail_on_closing(): void
    {
        $this->expectNotToPerformAssertions();
        $stream = MemoryStream::create();
        $stream->close();
        $stream->close();
        $stream->close();
    }

    #[Test]
    public function it_can_apply_actions_to_the_open_stream(): void
    {
        $stream = MemoryStream::create()
            ->apply(static fn ($stream) => fwrite($stream, 'hello'))
            ->apply(static fn ($stream) => rewind($stream));

        self::assertSame('hello', $stream->getContents());
    }
}
