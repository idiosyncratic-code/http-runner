<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Psr\Http\Message\ResponseInterface;

/**
 * Emit a response
 *
 * Takes a ResponseInterface instance and emits it to the client
 */
interface ResponseEmitter
{
    public function emit(ResponseInterface $response) : void;
}
