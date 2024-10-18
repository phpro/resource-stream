<?php

declare(strict_types=1);

namespace Unit\Exception;

use Phpro\ResourceStream\Exception\ResourceStreamException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResourceStreamException::class)]
class ResourceStreamExceptionTest extends TestCase
{
    #[Test]
    public function it_can_throw_no_resource(): void
    {
        $exception = ResourceStreamException::noResource();

        $this->expectExceptionObject($exception);
        $this->expectExceptionMessage('Expected an open resource stream.');
        throw $exception;
    }

    #[Test]
    public function it_can_throw_invalid_file(): void
    {
        $exception = ResourceStreamException::forFilePath('file.txt');

        $this->expectExceptionObject($exception);
        $this->expectExceptionMessage('Could not read file: "file.txt".');
        throw $exception;
    }

    #[Test]
    public function it_can_throw_invalid_stream_class(): void
    {
        $exception = ResourceStreamException::fromClass(new \stdClass());

        $this->expectExceptionObject($exception);
        $this->expectExceptionMessage('Could not get resource of given stream: "stdClass"');
        throw $exception;
    }
}
