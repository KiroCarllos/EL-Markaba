<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }


    public function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return api_response(0,__("site.Invalid or expired token"),"",401);
        }
        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }
    public function invalidJson($request, ValidationException $exception)
    {
        if (Request::wantsJson()) {
            foreach ($exception->errors() as $key => $value) {
                return api_response(0, $value[0], '',200,"ae",false);
            }
        }
    }
}
