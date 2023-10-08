<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    const CODE_EXCEPTION_VALIDATION = 142201;
    const CODE_EXCEPTION_CSRF = 140101;
    const CODE_EXCEPTION_UNAUTHORIZED = 140102;
    const CODE_EXCEPTION_AUTHORIZATION = 140301;
    const CODE_EXCEPTION_ACCESS_DENIED = 140303;
    const CODE_EXCEPTION_WITH_MESSAGE = 140305;
    const CODE_EXCEPTION_UNKNOWN_ROUTE = 140401;

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $exception) {
            // dump($exception);
        });

        $this->renderable(function (NotFoundHttpException $exception, Request $request) {
            if (auth()->guard('dashboard')->check() && $request->is('dashboard', 'dashboard/*')) {
                return response()->view('dashboard::errors.404', [], Response::HTTP_NOT_FOUND);
            }
            // return response()->view('errors.404', [], Response::HTTP_NOT_FOUND);
            // if ($request->is('api/*')) {
            //     return response()->json([
            //         'message' => 'Record not found.'
            //     ], 404);
            // }
        });
    }

    /**
     * Convert the given exception to an array.
     *
     * @param \Throwable $exception
     * @return array
     */
    protected function convertExceptionToArray(Throwable $exception)
    {
        $response = [];
        if ($exception instanceof AccessDeniedHttpException) {
            $response['code'] = self::CODE_EXCEPTION_ACCESS_DENIED;
        }
        if (config('app.debug')) {
            return [
                // 'error' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => collect($exception->getTrace())->map(fn ($trace) => Arr::except($trace, ['args']))->all(),
            ] + $response;
        }
        return [
            'error' => $exception->getStatusCode(),
            'message' => $this->isHttpException($exception) ? $exception->getMessage() : 'Server Error',
        ] + $response;
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Validation\ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'code' => self::CODE_EXCEPTION_VALIDATION,
            'error' => $exception->status,
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->shouldReturnJson($request, $exception)
            ? response()->json([
                'error' => Response::HTTP_UNAUTHORIZED,
                'code' => self::CODE_EXCEPTION_UNAUTHORIZED,
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNAUTHORIZED)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}
