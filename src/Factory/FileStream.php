<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\ResourceStream;

final class FileStream
{
    public const READ_MODE = 'rb';
    public const READ_WRITE_MODE = 'w+b';
    public const WRITE_MODE = 'wb';

    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function create(string $filePath, string $mode): ResourceStream
    {
        if (!file_exists($filePath)) {
            throw ResourceStreamException::forFilePath($filePath);
        }

        $resource = SafeStreamAction::run(
            static fn () => fopen($filePath, $mode),
            'Unable to open file "'.$filePath.'"'
        );

        return new ResourceStream($resource);
    }
}
