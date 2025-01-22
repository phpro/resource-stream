<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\ResourceStream;

final class CliStream
{
    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function stdout(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => fopen('php://stdout', 'w'),
            'Unable to open file "php://stdout"'
        );

        return new ResourceStream($resource);
    }

    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function stdin(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => fopen('php://stdin', 'r'),
            'Unable to open file "php://stdin"'
        );

        return new ResourceStream($resource);
    }

    /**
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function stderr(): ResourceStream
    {
        $resource = SafeStreamAction::run(
            static fn () => fopen('php://stderr', 'w'),
            'Unable to open file "php://stderr"'
        );

        return new ResourceStream($resource);
    }
}
