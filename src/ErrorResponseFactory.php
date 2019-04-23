<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Psr\Http\Message\ResponseInterface;
use Throwable;

interface ErrorResponseFactory
{
    /**
     * Returns an appropriate ResponseInterface instance for a PHP Exception/Throwable
     */
    public function createResponse(Throwable $error) : ResponseInterface;
}
