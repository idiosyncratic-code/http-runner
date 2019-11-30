<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\NullLogger;

class RunnerTest extends TestCase
{
    public function testOnlyResponseDataIsEmitted() : void
    {
        $request = $this->createStub(ServerRequestInterface::class);

        $response = $this->createStub(ResponseInterface::class);

        $requestHandler = $this->createStub(RequestHandlerInterface::class);
        $requestHandler->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(
                function () use ($response) {
                    echo 'Hello, World';
                    return $response;
                }
            ));

        $requestFactory = $this->createStub(ServerRequestFactory::class);

        $responseEmitter = $this->createStub(ResponseEmitter::class);
        $responseEmitter->expects($this->once())
            ->method('emit')
            ->will($this->returnCallback(
                function () {
                    echo 'response body';
                }
            ));

        ob_start();

        $runner = new Runner($requestFactory, $requestHandler, $responseEmitter, new NullLogger());

        $runner->run();

        $buffer = ob_get_contents();

        ob_end_clean();

        $this->assertEquals($buffer, 'response body');
    }

    public function testReturnsErrorResponseIfExceptionThrown() : void
    {
        $this->expectException(Exception::class);

        $request = $this->createStub(ServerRequestInterface::class);

        $response = $this->createStub(ResponseInterface::class);

        $requestHandler = $this->createStub(RequestHandlerInterface::class);
        $requestHandler->expects($this->once())
            ->method('handle')
            ->will($this->throwException(new Exception()));

        $requestFactory = $this->createStub(ServerRequestFactory::class);

        $responseEmitter = $this->createStub(ResponseEmitter::class);

        $runner = new Runner($requestFactory, $requestHandler, $responseEmitter, new NullLogger());

        $runner->run();
    }
}
