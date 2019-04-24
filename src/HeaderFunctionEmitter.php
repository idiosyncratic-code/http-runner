<?php

declare(strict_types=1);

namespace Idiosyncratic\Http\Runner;

use function header;
use function sprintf;

final class HeaderFunctionEmitter implements HeaderEmitter
{
    public function emitStatusLine(
        string $protocolVersion,
        int $statusCode,
        ?string $reasonPhrase = null
    ) : void {
        header(sprintf(
            'HTTP/%s %d %s',
            $protocolVersion,
            $statusCode,
            ($reasonPhrase ?? '')
        ), true, $statusCode);
    }

    public function emitHeader(string $header) : void
    {
        header($header, false);
    }
}
