<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Psr\Http\Message\ResponseInterface;
use function sprintf;
use function strtolower;

final class PhpSapiResponseEmitter implements ResponseEmitter
{
    /** @var HeaderEmitter */
    private $header;

    public function __construct(?HeaderEmitter $header = null)
    {
        $this->header = $header ?? new HeaderFunctionEmitter();
    }

    public function emit(ResponseInterface $response) : void
    {
        $this->header->emitStatusLine(
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $this->header->emitHeader(sprintf('%s: %s', strtolower($name), $value), false);
            }
        }

        print (string) $response->getBody();
    }
}
