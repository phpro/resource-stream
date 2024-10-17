<?php

declare(strict_types=1);

namespace Phpro\ResourceStreamTest\StaticAnalysis;

use Phpro\ResourceStream\Factory\MemoryStream;
use Phpro\ResourceStream\ResourceStream;

/**
 * @psalm-suppress UnusedParam
 *
 * @param ResourceStream<closed-resource> $stream
 */
function assertClosed(ResourceStream $stream): void
{
}

/**
 * @psalm-suppress UnusedParam
 *
 * @param ResourceStream<resource> $stream
 */
function assertOpened(ResourceStream $stream): void
{
}

$stream = MemoryStream::create();
assertOpened($stream);

$stream->close();
assertClosed($stream);
