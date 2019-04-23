<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use Psr\Http\Message\ServerRequestInterface;

interface ServerRequestFactory
{
    /**
     * Create a ServerRequestInterface
     *
     * Unlike the PSR-17 Psr\Http\Message\ServerRequestFactoryInterface, this
     * should generate a complete initial ServerRequestInterface from appropriate
     * data given the SAPI environment. For most SAPIs (i.e. mod_php or php-fpm),
     * this will likely be the $_COOKIE, $_GET, $_POST, $_FILES, and $_SERVER
     * superglobals
     */
    public function createServerRequest() : ServerRequestInterface;
}
