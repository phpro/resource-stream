<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Exception;

final class StreamActionFailureException extends RuntimeException
{
    public static function unableToRunAction(
        string $message,
        ?string $details = null,
    ): self {
        $message = $message.(null !== $details ? ' ('.$details.')' : '');

        return new self($message);
    }
}
