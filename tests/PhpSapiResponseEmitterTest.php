<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Exception;
use Idiosyncratic\Http\Runner\HeaderEmitter;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PhpSapiResponseEmitterTest extends TestCase
{
    public function testEmit() : void
    {
        $expectedContent = 'response body';

        $stream = $this->createStub(StreamInterface::class);
        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn($expectedContent);

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getBody')
            ->willReturn($stream);
        $response->method('getProtocolVersion')
            ->willReturn('1.1');
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getReasonPhrase')
            ->willReturn('OK');
        $response->method('getHeaders')
            ->willReturn(['X-HEADER' => ['foo']]);

        $headerEmitter = $this->createStub(HeaderEmitter::class);
        $headerEmitter->expects($this->once())
            ->method('emitStatusLine')
            ->with('1.1', 200, 'OK');
        $headerEmitter->expects($this->once())
            ->method('emitHeader')
            ->with('x-header: foo');

        $responseEmitter = new PhpSapiResponseEmitter($headerEmitter);

        ob_start();

        $responseEmitter->emit($response);

        $content = ob_get_contents();

        ob_end_clean();

        $this->assertEquals($content, $expectedContent);
    }

    private function getResponseMock() : ResponseInterface
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->setMethodsExcept([])
            ->getMock();
    }
}
