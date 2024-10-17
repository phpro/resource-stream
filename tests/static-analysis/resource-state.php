<?php

namespace Phpro\ResourceStreamTest\StaticAnalysis;

use Phpro\ResourceStream\Factory\MemoryStream;
use Phpro\ResourceStream\ResourceStream;

/**
 * @param ResourceStream<resource> $stream
 */
function assertClosed(ResourceStream $stream): void {}

/**
 * @param ResourceStream<closed-resource> $stream
 */
function assertOpened(ResourceStream $stream): void {}

$stream = MemoryStream::create();
assertOpened($stream);

$stream->close();
assertClosed($stream);
