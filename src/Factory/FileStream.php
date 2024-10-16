<?php

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Exception\StreamActionFailureException;
use Phpro\ResourceStream\ResourceStream;

final class FileStream
{
    public const READ_MODE = 'rb';
    public const WRITE_MODE = 'wb';

    /**
     * @throws ResourceStreamException
     * @throws StreamActionFailureException
     */
    public static function create(string $filePath, string $mode): ResourceStream
    {
        if (!file_exists($filePath)) {
            throw ResourceStreamException::forFilePath($filePath);
        }

        $resource = @fopen($filePath, $mode);
        if ($resource === false) {
            throw StreamActionFailureException::unableToOpen($filePath);
        }

        return new ResourceStream($resource);
    }
}
