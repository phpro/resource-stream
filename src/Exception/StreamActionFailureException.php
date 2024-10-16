<?php

namespace Phpro\ResourceStream\Exception;

final class StreamActionFailureException extends RuntimeException
{
    public readonly string $details;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        $lastError = error_get_last();
        $this->details = $lastError['message'] ?? '';
        $message = $message . ($this->details ? (' (' . $this->details . ')') : '');

        parent::__construct($message, $code, $previous);
    }

    public static function unableToOpen(string $location): self
    {
        return new self(sprintf('Failed to open stream: "%s".', $location));
    }

    public static function unableToRewind(): self
    {
        return new self('Failed to rewind resource stream.');
    }

    public static function unableToCopyStream(): self
    {
        return new self('Failed to copy resource stream.');
    }

    public static function unableToRead(): self
    {
        return new self('Failed to read contents of resource stream.');
    }

    public static function unableToStat(): self
    {
        return new self('Failed to stat resource stream.');
    }

    public static function unableToWrite(): self
    {
        return new self('Failed to write to resource stream.');
    }
}
