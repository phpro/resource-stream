<?php

declare(strict_types=1);

namespace Phpro\ResourceStream;

use Phpro\ResourceStream\ErrorHandling\SafeStreamAction;
use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Exception\RuntimeException;

/**
 * @psalm-type AnyResource = resource | closed-resource
 *
 * @template T of AnyResource
 */
final class ResourceStream
{
    private const DEFAULT_BUFFER_SIZE = 8192;
    private bool $keepAlive = false;

    /**
     * @param resource $resource
     *
     * @throws RuntimeException
     *
     * @psalm-this-out self<resource>
     */
    public function __construct(
        private mixed $resource,
    ) {
        $this->unwrap();
    }

    /**
     * @param mixed $resource
     *
     * @psalm-assert resource $resource
     *
     * @throws RuntimeException
     */
    public static function parse(mixed $resource): self
    {
        if (!is_resource($resource)) {
            throw ResourceStreamException::noResource();
        }

        return new self($resource);
    }

    public function __destruct()
    {
        if (!$this->keepAlive) {
            $this->close();
        }
    }

    /**
     * @return $this
     */
    public function keepAlive(): self
    {
        $this->keepAlive = true;

        return $this;
    }

    /**
     * @throws ResourceStreamException
     *
     * @return resource
     *
     * @psalm-this-out self<resource>
     */
    public function unwrap(): mixed
    {
        if (!$this->isOpen()) {
            throw ResourceStreamException::noResource();
        }

        /** @var resource */
        return $this->resource;
    }

    /**
     * @param \Closure(resource): void $closure
     *
     * @throws RuntimeException
     *
     * @return $this
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
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        return is_resource($this->resource);
    }

    /**
     * @throws RuntimeException
     *
     * @return string|null
     */
    public function uri(): ?string
    {
        $resource = $this->unwrap();
        $meta = stream_get_meta_data($resource);

        return $meta['uri'] ?? null;
    }

    /**
     * @throws RuntimeException
     *
     * @return $this
     */
    public function rewind(): self
    {
        $resource = $this->unwrap();

        SafeStreamAction::run(
            static fn (): bool => rewind($resource),
            'Failed to rewind resource stream.'
        );

        return $this;
    }

    /**
     * @param ResourceStream<resource> $targetStream
     *
     * @throws RuntimeException
     *
     * @return ResourceStream<resource>
     */
    public function copyTo(ResourceStream $targetStream, ?int $length = null, int $offset = 0): ResourceStream
    {
        $resource = $this->unwrap();
        $target = $targetStream->unwrap();

        SafeStreamAction::run(
            static fn (): int|false => stream_copy_to_stream($resource, $target, $length, $offset),
            'Failed to copy to resource stream.'
        );

        return $targetStream;
    }

    /**
     * @param ResourceStream<resource> $sourceStream
     *
     * @throws RuntimeException
     *
     * @return $this
     */
    public function copyFrom(ResourceStream $sourceStream, ?int $length = null, int $offset = 0): self
    {
        $source = $sourceStream->unwrap();
        $resource = $this->unwrap();

        SafeStreamAction::run(
            static fn (): int|false => stream_copy_to_stream($source, $resource, $length, $offset),
            'Failed to copy from resource stream.'
        );

        return $this;
    }

    /**
     * @psalm-this-out ResourceStream<closed-resource>
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

        return SafeStreamAction::run(
            static fn (): string|false => stream_get_contents($resource),
            'Failed to read contents of resource stream.'
        );
    }

    /**
     * @throws RuntimeException
     */
    public function size(): int
    {
        $resource = $this->unwrap();
        $stat = SafeStreamAction::run(
            static fn (): array|false => fstat($resource),
            'Failed to fstat of resource stream.'
        );

        return $stat['size'];
    }

    /**
     * @throws RuntimeException
     */
    public function read(int $length = self::DEFAULT_BUFFER_SIZE): string
    {
        $resource = $this->unwrap();

        return SafeStreamAction::run(
            static fn (): string|false => fread($resource, $length),
            'Failed to read contents of resource stream.'
        );
    }

    /**
     * @throws RuntimeException
     */
    public function readLine(int $length = self::DEFAULT_BUFFER_SIZE, string $ending = \PHP_EOL): string
    {
        $resource = $this->unwrap();

        return SafeStreamAction::run(
            static fn (): string|false => stream_get_line($resource, $length, $ending),
            'Failed to read contents of resource stream.'
        );
    }

    /**
     * @throws RuntimeException
     *
     * @return \Generator<int, string, mixed, void>
     */
    public function readLines(int $length = self::DEFAULT_BUFFER_SIZE, string $ending = \PHP_EOL): \Generator
    {
        while (!$this->isEof()) {
            yield $this->readLine($length, $ending);
        }
    }

    /**
     * @throws RuntimeException
     *
     * @return \Generator<int, string, mixed, void>
     */
    public function readBatches(int $length = self::DEFAULT_BUFFER_SIZE): \Generator
    {
        while (!$this->isEof()) {
            yield $this->read($length);
        }
    }

    /**
     * @throws RuntimeException
     *
     * @return $this
     */
    public function write(string $data): self
    {
        $resource = $this->unwrap();

        SafeStreamAction::run(
            static fn (): int|false => fwrite($resource, $data),
            'Failed to write to resource stream.'
        );

        return $this;
    }
}
