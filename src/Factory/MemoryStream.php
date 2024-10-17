<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\ResourceStream;

final class MemoryStream
{
    /**
     * @throws RuntimeException
     */
    public static function create(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => fopen('php://memory', 'w+b'),
            'Unable to open file "php://memory"'
        );

        return new ResourceStream($resource);
    }
}
