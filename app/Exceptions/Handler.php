<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Throwable;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof InvalidSignatureException && $request->expectsJson()) {
            return response()->json(['message' => 'Invalid or expired signature.'], 400);
        }

        return parent::render($request, $exception);
    }
}
