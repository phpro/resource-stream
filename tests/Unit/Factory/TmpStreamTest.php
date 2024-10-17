<?php

declare(strict_types=1);

namespace Unit\Factory;

use Phpro\ResourceStream\Factory\TmpStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TmpStream::class)]
class TmpStreamTest extends TestCase
{
    #[Test]
    public function it_can_open_tmp_file_stream(): void
    {
        $stream = TmpStream::create();

        self::assertTrue($stream->isOpen());
        self::assertStringContainsString(sys_get_temp_dir(), $stream->uri());
    }
}
