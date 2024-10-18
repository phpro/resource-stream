<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Exception;

final class ResourceStreamException extends RuntimeException
{
    public static function noResource(): self
    {
        return new self('Expected an open resource stream.');
    }

    public static function fromClass(object $class): self
    {
        return new self('Could not get resource of given stream: "'.get_class($class).'"');
    }

    public static function forFilePath(string $filePath): self
    {
        return new self('Could not read file: "'.$filePath.'".');
    }
}
