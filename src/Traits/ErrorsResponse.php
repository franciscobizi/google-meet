<?php

namespace FBIZI\Traits;

/**
 * ErrorsResponse
 *
 * @author Francisco Bizi <dev@dev.com>
 */
trait ErrorsResponse
{
    protected function show(int $code, string $message)
    {
        return ['code' => $code, 'message' => $message];
    }
}
