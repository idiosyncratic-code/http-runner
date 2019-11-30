<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use function header_remove;
use function ob_end_clean;
use function ob_get_level;
use function ob_start;

final class Runner
{
    /** @var ServerRequestFactory */
    private $request;

    /** @var RequestHandlerInterface */
    private $handler;

    /** @var ResponseEmitter */
    private $emitter;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ServerRequestFactory $request,
        RequestHandlerInterface $handler,
        ResponseEmitter $emitter,
        LoggerInterface $logger
    ) {
        $this->request = $request;

        $this->handler = $handler;

        $this->emitter = $emitter;

        $this->logger = $logger;
    }

    /**
     * Run a request.
     *
     * Handles an HTTP request by creating the initial ServerRequestInterface instance via the
     * ServerRequestFactory instance and then passing it to the RequestHandlerInterface instance.
     * If an exception is thrown, a ResponseInterface instance is created from the Exception by the
     * ErrorResponseFactory. Finally, the response is emitted back to the client by the ResponseEmitter
     * instance.
     *
     * Output buffering is used to prevent any content (including headers) from being sent back to the
     * client before ResponseEmitter::emit is called. A side effect of this is that any headers/cookies set
     * (including the session cookie) will be removed by the OB callback and must be added to the
     * returned ResponseInterface instance.
     */
    public function run() : void
    {
        $outputBuffers = ob_get_level();

        ob_start(static function (string $data, int $phase) : string {
            header_remove();

            return $data;
        });

        try {
            $response = $this->handler->handle($this->request->createServerRequest());
        } catch (Throwable $t) {
            $this->logger->error($t->getMessage(), ['exception' => $t]);

            throw $t;
        } finally {
            while (ob_get_level() > $outputBuffers) {
                ob_end_clean();
            }
        }

        $this->emitter->emit($response);
    }
}
