<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RunnerTest extends TestCase
{
    public function testOnlyResponseDataIsEmitted() : void
    {
        $request = $this->getServerRequestMock();

        $response = $this->getResponseMock();

        $requestHandler = $this->getRequestHandlerMock();
        $requestHandler->expects($this->once())
            ->method('handle')
            ->will($this->returnCallback(
                function () use ($response) {
                    echo 'Hello, World';
                    return $response;
                }
            ));

        $requestFactory = $this->getServerRequestFactoryMock();

        $errorFactory = $this->getErrorResponseFactoryMock();

        $responseEmitter = $this->getResponseEmitterMock();
        $responseEmitter->expects($this->once())
            ->method('emit')
            ->will($this->returnCallback(
                function () {
                    echo 'response body';
                }
            ));

        ob_start();

        $runner = new Runner($requestFactory, $requestHandler, $responseEmitter, $errorFactory);

        $runner->run();

        $buffer = ob_get_contents();

        ob_end_clean();

        $this->assertEquals($buffer, 'response body');
    }

    public function testErrorResponseNotCalledIfNoExceptionRaised() : void
    {
        $request = $this->getServerRequestMock();

        $response = $this->getResponseMock();

        $requestHandler = $this->getRequestHandlerMock();

        $requestFactory = $this->getServerRequestFactoryMock();

        $errorFactory = $this->getErrorResponseFactoryMock();
        $errorFactory->expects($this->never())
            ->method('createResponse');

        $responseEmitter = $this->getResponseEmitterMock();

        $runner = new Runner($requestFactory, $requestHandler, $responseEmitter, $errorFactory);

        $runner->run();
    }

    public function testReturnsErrorResponseIfExceptionThrown() : void
    {
        $request = $this->getServerRequestMock();

        $response = $this->getResponseMock();

        $requestHandler = $this->getRequestHandlerMock();
        $requestHandler->expects($this->once())
            ->method('handle')
            ->will($this->throwException(new Exception()));

        $requestFactory = $this->getServerRequestFactoryMock();

        $errorFactory = $this->getErrorResponseFactoryMock();
        $errorFactory->expects($this->once())
            ->method('createResponse');


        $responseEmitter = $this->getResponseEmitterMock();

        $runner = new Runner($requestFactory, $requestHandler, $responseEmitter, $errorFactory);

        $runner->run();
    }

    private function getServerRequestMock() : ServerRequestInterface
    {
        return $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethodsExcept([])
            ->getMock();
    }

    private function getResponseMock() : ResponseInterface
    {
        return $this->getMockBuilder(ResponseInterface::class)
            ->setMethodsExcept([])
            ->getMock();
    }

    private function getRequestHandlerMock() : RequestHandlerInterface
    {
        return $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();
    }


    private function getServerRequestFactoryMock() : ServerRequestFactory
    {
        return $this->getMockBuilder(ServerRequestFactory::class)
            ->setMethods(['createServerRequest'])
            ->getMock();
    }

    private function getErrorResponseFactoryMock() : ErrorResponseFactory
    {
        return $this->getMockBuilder(ErrorResponseFactory::class)
            ->setMethods(['createResponse'])
            ->getMock();
    }

    private function getResponseEmitterMock() : ResponseEmitter
    {
        return $this->getMockBuilder(ResponseEmitter::class)
            ->setMethods(['emit'])
            ->getMock();
    }
}
