<?php

declare(strict_types=1);

namespace Unit\Factory;

use Phpro\ResourceStream\Factory\MemoryStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(MemoryStream::class)]
class MemoryStreamTest extends TestCase
{
    #[Test]
    public function it_can_open_memory_stream(): void
    {
        $stream = MemoryStream::create();

        self::assertTrue($stream->isOpen());
    }
}
