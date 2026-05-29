<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\ResourceStream;

final class ZipStream
{
    public const READ_MODE = 'rb';

    /**
     * @param array<string, mixed> $zipOptions Options passed to stream_context_create() under the 'zip' key (e.g. ['password' => '...']).
     *
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public static function read(string $archivePath, string $entry, array $zipOptions = []): ResourceStream
    {
        if (!file_exists($archivePath)) {
            throw ResourceStreamException::forFilePath($archivePath);
        }

        $uri = 'zip://'.$archivePath.'#'.$entry;
        $context = [] === $zipOptions ? null : stream_context_create(['zip' => $zipOptions]);

        $resource = SafeStreamAction::run(
            static fn () => null !== $context
                ? fopen($uri, self::READ_MODE, false, $context)
                : fopen($uri, self::READ_MODE),
            'Unable to open zip entry "'.$entry.'" in archive "'.$archivePath.'"'
        );

        return new ResourceStream($resource);
    }
}
