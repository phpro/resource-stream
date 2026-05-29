<?php

declare(strict_types=1);

namespace Phpro\ResourceStream\Tests\Unit\Factory;

use Phpro\ResourceStream\Exception\ResourceStreamException;
use Phpro\ResourceStream\Exception\StreamActionFailureException;
use Phpro\ResourceStream\Factory\ZipStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ZipStream::class)]
class ZipStreamTest extends TestCase
{
    private string $archivePath;
    private string $encryptedArchivePath;

    protected function setUp(): void
    {
        $this->archivePath = sys_get_temp_dir().'/rs-zip-'.uniqid().'.zip';
        $zip = new \ZipArchive();
        $zip->open($this->archivePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('hello.txt', 'Hello World');
        $zip->addFromString('nested/file.txt', 'Nested contents');
        $zip->close();

        $this->encryptedArchivePath = sys_get_temp_dir().'/rs-zip-enc-'.uniqid().'.zip';
        $enc = new \ZipArchive();
        $enc->open($this->encryptedArchivePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $enc->setPassword('secret');
        $enc->addFromString('secret.txt', 'Top Secret');
        $enc->setEncryptionName('secret.txt', \ZipArchive::EM_AES_256);
        $enc->close();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->archivePath)) {
            unlink($this->archivePath);
        }
        if (file_exists($this->encryptedArchivePath)) {
            unlink($this->encryptedArchivePath);
        }
    }

    #[Test]
    public function it_can_not_open_unexisting_archive(): void
    {
        $this->expectException(ResourceStreamException::class);
        $this->expectExceptionMessage('Could not read file: "/unexisting/archive.zip"');

        ZipStream::read('/unexisting/archive.zip', 'hello.txt');
    }

    #[Test]
    public function it_can_read_an_entry_from_an_archive(): void
    {
        $stream = ZipStream::read($this->archivePath, 'hello.txt');

        self::assertTrue($stream->isOpen());
        self::assertSame('Hello World', $stream->getContents());
    }

    #[Test]
    public function it_can_read_a_nested_entry(): void
    {
        $stream = ZipStream::read($this->archivePath, 'nested/file.txt');

        self::assertSame('Nested contents', $stream->getContents());
    }

    #[Test]
    public function it_fails_to_open_a_missing_entry(): void
    {
        $this->expectException(StreamActionFailureException::class);
        $this->expectExceptionMessage('Unable to open zip entry "does-not-exist.txt"');

        ZipStream::read($this->archivePath, 'does-not-exist.txt');
    }

    #[Test]
    public function it_can_read_an_encrypted_entry_with_password(): void
    {
        $stream = ZipStream::read($this->encryptedArchivePath, 'secret.txt', ['password' => 'secret']);

        self::assertSame('Top Secret', $stream->getContents());
    }

    #[Test]
    public function it_fails_to_read_an_encrypted_entry_without_password(): void
    {
        $this->expectException(StreamActionFailureException::class);

        $stream = ZipStream::read($this->encryptedArchivePath, 'secret.txt');
        $stream->getContents();
    }
}
