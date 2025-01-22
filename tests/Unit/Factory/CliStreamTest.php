<?php

declare(strict_types=1);

namespace Unit\Factory;

use Phpro\ResourceStream\Factory\CliStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CliStream::class)]
class CliStreamTest extends TestCase
{
    #[Test]
    public function it_can_open_input_cli_stream(): void
    {
        $stream = CliStream::stdin();

        self::assertTrue($stream->isOpen());
        self::assertStringContainsString('php://stdin', $stream->uri());
    }

    #[Test]
    public function it_can_open_output_cli_stream(): void
    {
        $stream = CliStream::stdout();

        self::assertTrue($stream->isOpen());
        self::assertStringContainsString('php://stdout', $stream->uri());
    }

    #[Test]
    public function it_can_open_error_cli_stream(): void
    {
        $stream = CliStream::stderr();

        self::assertTrue($stream->isOpen());
        self::assertStringContainsString('php://stderr', $stream->uri());
    }
}
