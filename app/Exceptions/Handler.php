<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        DoubleRegistration::class,
        NotEnoughBalance::class,
        TelephoneUnconfirmed::class
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
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render($request, Exception $exception)
    {
        $userLevelCheck = $exception instanceof \jeremykenedy\LaravelRoles\Exceptions\RoleDeniedException ||
            $exception instanceof \jeremykenedy\LaravelRoles\Exceptions\RoleDeniedException ||
            $exception instanceof \jeremykenedy\LaravelRoles\Exceptions\PermissionDeniedException ||
            $exception instanceof \jeremykenedy\LaravelRoles\Exceptions\LevelDeniedException;

        if ($userLevelCheck) {

            if ($request->expectsJson()) {
                return Response::json(array(
                    'error'    =>  403,
                    'message'   =>  'Unauthorized.'
                ), 403);
            }

            abort(403);
        }

        if($exception instanceof NotEnoughBalance) {
            $message = 'Для совершения данного действия необходимо пополнить баланс на сумму не менее ' . $exception->getAmount() . ' руб. ';
            $message .= $exception->getMessage();
            if($request->expectsJson()) {
                return Response::json([
                    'error' => 403,
                    'message' => $message,
                    'header' => 'Недостаточно средств'
                ]);
            }

            return redirect()->route('account.transactions.index')
                ->with(['status' => 'warning', 'message' => $message]);
        }

        if($exception instanceof AccountForbidden) {
            if($request->expectsJson()) {
                return Response::json([
                    'error' => 401,
                    'message' => $exception->getMessage(),
                    'header' => 'Необходимо действие'
                ]);
            }

            return redirect()->route('account.noAccessSection');
        }

        if($exception instanceof DoubleRegistration) {
            return redirect()->route('register.double-registration', $exception->getData()['user']);
        }

        if($exception instanceof TelephoneUnconfirmed) {
            if($request->expectsJson()) {
                return Response::json([
                    'errors' => [
                        'telephone_confirm' => true
                    ]
                ]);
            }

            return redirect()->route('account.index')
                ->with(['status' => 'warning', 'message' => 'Необходимо подтвердить номер телефона']);
        }

        return parent::render($request, $exception);
    }
}
