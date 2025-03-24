<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Tests\Unit\Exception;

use Phpro\ResourceStream\Exception\StreamActionFailureException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StreamActionFailureException::class)]
class StreamActionFailureExceptionTest extends TestCase
{
    #[Test]
    public function it_throws_on_invalid_action(): void
    {
        $exception = StreamActionFailureException::unableToRunAction('Action failed');

        $this->expectExceptionObject($exception);
        $this->expectExceptionMessage('Action failed');
        throw $exception;
    }

    #[Test]
    public function it_throws_on_invalid_action_with_details(): void
    {
        $exception = StreamActionFailureException::unableToRunAction('Action failed', 'Details');

        $this->expectExceptionObject($exception);
        $this->expectExceptionMessage('Action failed (Details)');
        throw $exception;
    }
}
