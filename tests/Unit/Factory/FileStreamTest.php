<?php

declare(strict_types=1);

namespace Unit\Factory;

use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Factory\FileStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileStream::class)]
class FileStreamTest extends TestCase
{
    #[Test]
    public function it_can_not_open_unexisting_file(): void
    {
        $this->expectException(ResourceStreamException::class);
        $this->expectExceptionMessage('Could not read file: "/unexisting/file.txt"');

        FileStream::create('/unexisting/file.txt', FileStream::READ_MODE);
    }

    #[Test]
    public function it_can_open_existing_file(): void
    {
        $stream = FileStream::create(__DIR__.'/../../fixtures/hello.txt', FileStream::READ_MODE);

        self::assertTrue($stream->isOpen());
    }
}
