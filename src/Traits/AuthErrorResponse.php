<?php

namespace FBIZI\Traits;

/**
 * Auth Error Response
 *
 * @author Francisco Bizi <dev@dev.com>
 */
trait AuthErrorResponse
{
    protected function missingToken(string $message = "")
    {
        $message = !empty($message) ? $message : "Missing token. Get token before procceed to this action.";
        return ['code' => 401, 'message' => $message];
    }

    protected function unauthorized(string $message = "")
    {
        $message = !empty($message) ? $message : "Don't have permission to access it.";
        return ['code' => 403, 'message' => $message];
    }
}
