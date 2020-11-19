<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

/**
 * 格式化 api JSON输出 中间件
 * Class FormatResult
 * @package App\Http\Middleware
 */
class FormatResult
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Pre-Middleware Action

        $response = $next($request);

        if ($response instanceof RedirectResponse) {
            return $response;
        }

        if (!$request->ajax()
            && !$request->is('api/*')
            && !$request->is('admin/api/*')) {
            return $response;
        }

        // Post-Middleware Action

        if (!empty($response->exception) && $response->exception instanceof \Exception) {
            /**
             * @var \Exception $exception
             */
            $exception = $response->exception;

            $debug = config('app.debug');

            $error = [
                'msg' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ];

            if ($debug) {
                $error['file'] = $exception->getFile();
                $error['line'] = $exception->getLine();
//                $error['trace'] = $exception->getTrace();
            }

            if ($exception instanceof ValidationException) {
                $error['validate'] = $exception->validator->getMessageBag();
            }
            $response = response()->json([
                'status' => 0,
                'error' => $error,
            ]);
        } else {
            $response = response()->json([
                'status' => 1,
                'data' => $response->original,
            ]);
        }

        return $response;
    }
}
