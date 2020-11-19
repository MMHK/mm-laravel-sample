<?php

namespace App\Http\Middleware;

use Closure;

/**
 * layout 渲染中间件，用于挟持修改view的最新的渲染结果
 *
 * Class RenderLayout
 * @package App\Http\Middleware
 */
class RenderLayout
{

    protected $except = [
    ];
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach ($this->except as $except) {
            if ($request->is($except)) {
                return $next($request);
            }
        }
        /**
         * 监听view的注入事件，强行增加view的渲染计数，阻止section被清空
         */
        $render_count = 0;
        app('events')->listen('composing:*', function() use(& $render_count) {
            view()->incrementRender();
            $render_count++;
        });
        $response = $next($request);


        /**
         * 先看看是不是 Response对象
         */
        if (method_exists($response, 'getOriginalContent')) {
            $original = $response->getOriginalContent();

            /**
             * 看看是不是 view 对象及有没有设置到 layout
             */
            if ($original instanceof \Illuminate\View\View) {
                /**
                 * 判断一下 layout 是不是空的
                 * @var $original \Illuminate\View\View
                 */
                $layout_share = \Arr::get($original->getFactory()->getShared(), 'layout');
                $layout_alise = $layout_share ?: false;
                if ($original->offsetExists('layout')) {
                    $layout_alise = $original->offsetGet('layout');
                }
                if ($layout_alise) {
                    /**
                     *  清除被强加的渲染计数
                     */
                    while($render_count > 0) {
                        view()->decrementRender();
                        $render_count--;
                    }

                    /**
                     * 重新将response 装进layout里面。
                     */
                    $content = $response->getContent();
                    $response->setContent(view($layout_alise)
                        ->with('content', $content)
                        ->with($original->getData())
                        );
                }
            }
        }

        return $response;
    }
}
