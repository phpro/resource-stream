<?php

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\Exception\StreamActionFailureException;
use Phpro\ResourceStream\ResourceStream;

final class TmpStream
{
    /**
     * @throws StreamActionFailureException
     */
    public static function create(): ResourceStream
    {
        $tmpFile = \tmpfile();
        if ($tmpFile === false) {
            throw StreamActionFailureException::unableToOpen('tmpfile()');
        }

        return new ResourceStream($tmpFile);
    }
}
