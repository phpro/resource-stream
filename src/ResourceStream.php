<?php

namespace Phpro\ResourceStream;

use Phpro\ResourceStream\Exception\RuntimeException;
use Phpro\ResourceStream\Exception\StreamActionFailureException;
use Phpro\ResourceStream\Exception\ResourceStreamException;

/**
 * @template T of resource|closed-resource
 */
final class ResourceStream
{
    /**
     * @param T $resource
     */
    public function __construct(
        private $resource,
    ) {
    }

    /**
     * @throws ResourceStreamException
     *
     * @return resource
     * @psalm-if-this-is self<resource>
     */
    public function unwrap()
    {
        if (!$this->isOpen()) {
            throw ResourceStreamException::noResource();
        }

        return $this->resource;
    }

    /**
     * @param \Closure(resource): void $closure
     * @return self
     */
    public function apply(\Closure $closure): self
    {
        $closure($this->unwrap());

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function isEof(): bool
    {
        return feof($this->unwrap());
    }

    public function isOpen(): bool
    {
        return is_resource($this->resource);
    }

    /**
     * @return string|null
     * @throws RuntimeException
     */
    public function uri(): ?string
    {
        return stream_get_meta_data($this->unwrap())['uri'] ?? null;
    }

    /**
     * @throws RuntimeException
     */
    public function rewind(): self
    {
        $resource = $this->unwrap();
        $result = rewind($resource);
        if ($result === false) {
            throw StreamActionFailureException::unableToRewind();
        }

        return $this;
    }

    /**
     * @throws RuntimeException
     */
    public function copyTo(ResourceStream $targetStream, ?int $length = null, int $offset = 0): ResourceStream
    {
        $resource = $this->unwrap();
        $target = $targetStream->unwrap();

        $result = stream_copy_to_stream($resource, $target, $length, $offset);
        if ($result === false) {
            throw StreamActionFailureException::unableToCopyStream();
        }

        return $targetStream;
    }

    /**
     * @throws RuntimeException
     */
    public function copyFrom(ResourceStream $sourceStream, ?int $length = null, int $offset = 0): self
    {
        $source = $sourceStream->unwrap();
        $resource = $this->unwrap();

        $result = stream_copy_to_stream($source, $resource, $length, $offset);
        if ($result === false) {
            throw StreamActionFailureException::unableToCopyStream();
        }

        return $this;
    }

    /**
     * @psalm-this-out ResourceStream<closed-resource>
     * @throws RuntimeException
     */
    public function close(): void
    {
        try {
            $resource = $this->unwrap();
            @fclose($resource);
        } catch (ResourceStreamException) {
            // Ignore: Assuming already closed.
        }
    }

    /**
     * @throws RuntimeException
     */
    public function getContents(): string
    {
        $resource = $this->unwrap();
        $content = stream_get_contents($resource);
        if ($content === false) {
            throw StreamActionFailureException::unableToRead();
        }

        return $content;
    }

    /**
     * @throws RuntimeException
     */
    public function size(): int
    {
        $stat = @fstat($this->unwrap());
        if ($stat === false) {
            throw StreamActionFailureException::unableToStat();
        }

        return $stat['size'] ?? 0;
    }

    /**
     * @throws RuntimeException
     */
    public function read(int $length = null): string
    {
        $length ??= $this->size();
        $resource = $this->unwrap();
        $content = @fread($resource, $length);

        if ($content === false) {
            throw StreamActionFailureException::unableToRead();
        }

        return $content;
    }

    /**
     * @throws RuntimeException
     */
    public function write(string $data): self
    {
        $resource = $this->unwrap();
        $result = @fwrite($resource, $data);

        if ($result === false) {
            throw StreamActionFailureException::unableToWrite();
        }

        return $this;
    }
}
