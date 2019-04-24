<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

interface HeaderEmitter
{
    public function emitStatusLine(
        string $protocolVersion,
        int $statusCode,
        ?string $reasonPhrase = null
    ) : void;

    public function emitHeader(string $header) : void;
}
