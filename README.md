# The Idiosyncratic HTTP Runner
Library for running an HTTP request and emitting the generated response.

Inspired by [zend-httphandlerrunner](https://docs.zendframework.com/zend-httphandlerrunner/), this library
provides a class for executing [PSR-15](https://www.php-fig.org/psr/psr-15) request handlers and emitting
the generated [PSR-7](https://www.php-fig.org/psr/psr-7) response back to the waiting client. Differences from  
the Zend library include:

- Requires an implementation of `Idiosyncratic\Http\Runner\ServerRequestFactory` to creates the initial PSR-7 `ServerRequestInterface` instance instead of accepting a callable
- Requires an implementation of `Idiosyncratic\Http\Runner\ErrorResponseFactory` to handle generating a PSR-7 `ResponseInterface` instance for Exceptions instead of accepting a callable
- Internally, uses [output buffering](https://www.php.net/manual/en/book.outcontrol.php) to suppress output to the client, including headers, until the final response is emitted. As a result, all headers and cookies (including for instance the PHP session cookie) must be set in the final `ResponseInterface` instance or they will not be sent to the client
- The interface for the response emitter (`Idiosyncratic\Http\Runner\ResponseEmitter`) is not stackable

## Installation
Install using [Composer](https://getcomposer.org):

```
composer require idiosyncratic/http-runner
```

## Usage
In order to user this library, you will need to provide:
- An implementation of `Psr\Http\Server\RequestHandlerInterface` to handle generating a response
- An implementation of `Idiosyncratic\Http\Runner\ServerRequestFactory` to create the initial PSR-7 `ServerRequestInterface` instance
- An implementation of `Idiosyncratic\Http\Runner\ErrorResponseFactory` to handle creating a fallback error response in case of Exception
- An implementation of `Idiosyncratic\Http\Runner\ResponseEmitter` to emit a PSR-7 `ResponseInterface` to the client. A basic `Idiosyncratic\Http\Runner\PhpSapiResponseEmitter` is included.

```
use Idiosyncratic\Http\Runner\Runner;
use Idiosyncratic\Http\Runner\PhpSapiResponseEmitter;

$runner = new Runner(
    $serverRequestFactoryImplementation,
    $requestHandlerImplementation,
    new PhpSapiResponseEmitter(),
    $errorResponseFactoryImplementation
);

$runner->run();
```
