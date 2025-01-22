# Resource Stream

A safe OOP wrapper around resource [streams](https://www.php.net/manual/en/book.stream.php) in PHP:

* No need to worry about streams that have been closed.
* Instead of returning `false`, detailed exceptions are thrown when something goes wrong.
* Streams are automatically closed when the object is destructed (with option to opt-out).
* Handy factory methods for common streams.

## Installation

```bash
composer require phpro/resource-stream
```

## Usage

```php
use Phpro\ResourceStream\ResourceStream;

$stream = (new ResourceStream(fopen('php://temp', 'r+')))
    ->write('Hello World')
    ->rewind();

// Various ways to read:
echo $stream->read();
echo $stream->read($bufferSize);
echo $stream->readLine();
echo $stream->readLine($bufferSize, ending: \PHP_EOL);
echo $stream->getContents();

// Or in batches (Generator<string>)
$cursor = $stream->readBatches($bufferSize);
$cursor = $stream->readLines($bufferSize, ending: \PHP_EOL); 

// Get access to PHP's inner resource stream
$innerStream = $stream->unwrap();

// Get access to common information:
$stream->isOpen();
$stream->isEof();
$stream->uri();
$stream->size();

// Possibility to copy contents across streams
$stream->copyTo($anotherStream);
$stream->copyFrom($anotherStream);

// Streams will automatically be closed on destruction.
// Of course, you can choose to keep it open or close it manually:
$stream->keepAlive();
$stream->close();
```

## Built-in Streams

The following streams are available by default:

### CliStream

Can be used to open up CLI `stdin`, `stdout` and `stderr` streams.

```php
use Phpro\ResourceStream\Factory\CliStream;

$stdin = CliStream::stdin();
$stdout = CliStream::stdout();
$stderr = CliStream::stderr();
```

### FileStream

Validates if the local file exists and opens it up for you to use.

```php
use Phpro\ResourceStream\Factory\FileStream;

$stream = FileStream::create('/path/to/file', FileStream::READ_WRITE_MODE);
```

### IOStream

Can be used to open up CGI `php://input` and `php://output` streams.

```php
use Phpro\ResourceStream\Factory\IOStream;

$input = IOStream::input();
$output = IOStream::output();
```

### MemoryStream

Creates an in-memory stream for you to use.

```php
use Phpro\ResourceStream\Factory\MemoryStream;

$stream = MemoryStream::create();
```

### Psr7Stream

Creates a stream from a PSR-7 stream / request / response.
before you can use this stream, you'll need to install the `guzzlehttp/psr-7` package which contains a stream wrapper implementation.

```bash
composer require psr/http-message guzzlehttp/psr-7
```

```php
use Phpro\ResourceStream\Factory\Psr7Stream;

$stream = Psr7Stream::createFromStream($anyPsr7Stream);
$stream = Psr7Stream::createFromRequest($anyPsr7Request);
$stream = Psr7Stream::createFromResponse($anyPsr7Response);
```

### TmpStream

Creates a temporary file and opens it up for you to use.
After the stream is closed, the temporary file will be removed.

```php
use Phpro\ResourceStream\Factory\TmpStream;

$stream = TmpStream::create();
```
