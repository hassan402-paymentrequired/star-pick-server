<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // $logAndJson = function (Throwable $e, Request $request, int $status, string|null $message = null) {
        //     Log::error($e->getMessage(), [
        //         'exception' => get_class($e),
        //         'file' => $e->getFile(),
        //         'line' => $e->getLine(),
        //         'url' => $request->fullUrl(),
        //         'method' => $request->method(),
        //     ]);

        //     return response()->json([
        //         'error' => $message ?: $e->getMessage(),
        //         'message' => 'unable to proccess your request please try again later',
        //         'code' => $e->getCode()
        //     ], $status);
        // };

        // $exceptions->render(fn(ModelNotFoundException $e, Request $r) => $logAndJson($e, $r, 404, 'Record not found.'));
        // $exceptions->render(fn(AuthenticationException $e, Request $r) => $logAndJson($e, $r, 401, 'Unauthenticated. Please login.'));
        // $exceptions->render(fn(MethodNotAllowedHttpException $e, Request $r) => $logAndJson($e, $r, 405, 'The method is not allowed.'));
        // $exceptions->render(fn(QueryException $e, Request $r) => $logAndJson($e, $r, 500, 'Something went wrong.'));
        // $exceptions->render(fn(NotFoundHttpException $e, Request $r) => $logAndJson($e, $r, 404));
        // $exceptions->render(fn(ValidationException $e, Request $r) => response()->json(['errors' => $e->errors()], 422));
        // $exceptions->render(fn(RouteNotFoundException $e, Request $r) => $logAndJson($e, $r, 404));
        // $exceptions->render(fn(AuthorizationException $e, Request $r) => $logAndJson($e, $r, 403));
        // $exceptions->render(fn(UnauthorizedHttpException $e, Request $r) => $logAndJson($e, $r, 401));
        // $exceptions->render(fn(PDOException $e, Request $r) => $logAndJson($e, $r, 401, 'Something went wrong.'));
        // $exceptions->render(fn(Throwable $e, Request $r) => $logAndJson($e, $r, 500, 'Something went wrong.'));
        $exceptions->renderable(function (\Throwable $e, $request) {
            return app(\App\Exceptions\Handler::class)->render($request, $e);
        });
    })->create();
