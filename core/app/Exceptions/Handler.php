<?php

namespace App\Exceptions;

use App\Models\AdminNotification;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for this application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            $notify[] = ['error', 'Your session expired. Please try again.'];

            if ($request->is('user/login')) {
                $target = url('/user/login');
            } elseif ($request->is('user/register') || $request->is('user/check-mail')) {
                $target = url('/user/register');
                $ref = trim((string) ($request->input('inviter_code') ?: $request->input('ref') ?: session('ref')));
                if ($ref !== '') {
                    $target .= '?ref=' . urlencode($ref);
                }
            } else {
                $target = url()->previous() ?: url('/');
            }

            return redirect()->to($target)
                ->withInput($request->except('_token', 'password', 'password_confirmation'))
                ->withNotify($notify);
        }

        return parent::render($request, $e);
    }
}
