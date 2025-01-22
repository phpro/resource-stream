<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\ResourceStream;

final class IOStream
{
    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function input(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => fopen('php://input', FileStream::READ_MODE),
            'Unable to open file "php://input"'
        );

        return new ResourceStream($resource);
    }

    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function output(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => fopen('php://output', FileStream::WRITE_MODE),
            'Unable to open file "php://output"'
        );

        return new ResourceStream($resource);
    }
}
