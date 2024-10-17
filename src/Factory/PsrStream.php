<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Factory;

use GuzzleHttp\Psr7\StreamWrapper;
use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\ResourceStream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class PsrStream
{
    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws ResourceStreamException
     *
     * @return ResourceStream<resource>
     */
    public static function createFromStream(StreamInterface $stream): ResourceStream
    {
        // @codeCoverageIgnoreStart
        // StreamWrapper is always available in test-suite.
        if (!class_exists(StreamWrapper::class)) {
            throw new \RuntimeException('Please run: "composer require guzzle/psr-7" if you want to load a PSR-7 resource stream.');
        }
        /** @codeCoverageIgnoreEnd */
        $resource = StreamWrapper::getResource($stream);
        // @codeCoverageIgnoreStart
        // Theoretically, getResource could return `false`. No test-case found for this.
        /** @psalm-suppress DocblockTypeContradiction */
        if (!is_resource($resource)) {
            throw ResourceStreamException::fromClass($stream);
        }
        // @codeCoverageIgnoreEnd

        return new ResourceStream($resource);
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws ResourceStreamException
     *
     * @return ResourceStream<resource>
     */
    public static function createFromRequest(RequestInterface $request): ResourceStream
    {
        return self::createFromStream($request->getBody());

    }

    /**
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws ResourceStreamException
     *
     * @return ResourceStream<resource>
     */
    public static function createFromResponse(ResponseInterface $response): ResourceStream
    {
        return self::createFromStream($response->getBody());
    }
}
