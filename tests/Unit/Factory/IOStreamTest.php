<?php

declare(strict_types=1);

namespace Unit\Factory;

use Phpro\ResourceStream\Factory\IOStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(IOStream::class)]
class IOStreamTest extends TestCase
{
    #[Test]
    public function it_can_open_input_io_stream(): void
    {
        $stream = IOStream::input();

        self::assertTrue($stream->isOpen());
        self::assertStringContainsString('php://input', $stream->uri());
    }

    #[Test]
    public function it_can_open_output_io_stream(): void
    {
        $stream = IOStream::output();

        self::assertTrue($stream->isOpen());
        self::assertStringContainsString('php://output', $stream->uri());
    }
}
