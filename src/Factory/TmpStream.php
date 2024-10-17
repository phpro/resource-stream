<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\ResourceStream;

final class TmpStream
{
    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function create(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => \tmpfile(),
            'Unable to open temporary file'
        );

        return new ResourceStream($resource);
    }
}
