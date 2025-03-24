<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Tests\Unit\ErrorHandling;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\StreamActionFailureException;
use Phpro\ResourceStream\Factory\MemoryStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SafeStreamAction::class)]
final class SafeStreamActionTest extends TestCase
{
    #[Test]
    public function it_can_successfully_run_action(): void
    {
        $stream = MemoryStream::create();
        $result = SafeStreamAction::run(
            static fn () => fwrite($stream->unwrap(), 'hello'),
            'Action failed'
        );

        self::assertSame(5, $result);
    }

    #[Test]
    public function it_can_fail_running_an_action(): void
    {
        $stream = fopen('php://memory', 'r');

        $this->expectException(StreamActionFailureException::class);
        $this->expectExceptionMessage('Action failed');

        SafeStreamAction::run(
            static fn () => fwrite($stream, 'hello'),
            'Action failed'
        );

        fclose($stream);
    }

    #[Test]
    public function it_can_fail_running_an_action_with_system_details(): void
    {
        $this->expectException(StreamActionFailureException::class);
        $this->expectExceptionMessage('Action failed (fopen(doesnotexist): Failed to open stream: No such file or directory)');

        SafeStreamAction::run(
            static fn () => fopen('doesnotexist', 'r'),
            'Action failed'
        );
    }
}
