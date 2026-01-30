<?php

namespace App\Utils;

use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Throwable;

class ApiExceptionHandler
{
    public static function handle(Throwable $e, $request)
    {
        // Only handle API routes
        if (!$request->is('api/*')) {
            return null;
        }

        // Validation errors → 422
        if ($e instanceof ValidationException) {
            $errors = $e->validator->errors();
            return ApiResponse::fail(
                message: $errors->first(),
                errors: $errors,
                code: 422
            );
        }

        // Throttle (too many requests) → 429
        if ($e instanceof ThrottleRequestsException) {
            return ApiResponse::fail(
                message: $e->getMessage(),
                code: 429,
                errors: [
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? null
                ]
            );
        }

        if ($e instanceof HttpException) {
            $errorCode = $e->getStatusCode();
            return ApiResponse::fail(
                message: $e->getMessage(),
                code: $errorCode
            );
        }
        // Authentication errors → 401
        if($e instanceof AuthenticationException){
            return ApiResponse::fail(
                message: $e->getMessage(),
                code: 401,
            );
        }

        // Server errors → 500
        if ($e instanceof InternalErrorException || $e instanceof \Error || $e instanceof \ErrorException) {
            if (config('app.debug')) {
                return ApiResponse::fail(
                    message: $e->getMessage(),
                    code: $e->getCode() ?: 500,
                    httpStatus: 500,
                    errors: [
                        'trace' => $e->getTrace()
                    ]
                );
            }

            return ApiResponse::fail(
                message: 'Internal server error',
                code: $e->getCode() ?: 500,
                httpStatus: 500
            );
        }

        // Fallback for any other exception
        $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

        return ApiResponse::fail(
            message: $e->getMessage(),
            code: $status,
            httpStatus: $status,
            errors: [
                'trace' => config('app.debug') ? $e->getTrace() : null
            ]
        );
    }

}
