<?php

namespace App\Providers;

use App\Helper\MM\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     *
     * @return void
     * @throws \Doctrine\DBAL\DBALException
     */
    public function boot()
    {
        /**
         * 只在local 环境才会引入开发工具的provider
         */
        if ($this->app->environment(ENV_DEV)) {

            /**
             *  引入 IDE helper
             */
            $this->app->register(
                'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider'
            );

            /**
             * 本地开发模式下，所有的Mail发送输出到log
             */
            \Config::set('mail.driver', 'log');
        }

        /**
         * 注入自定义的url 接口
         **/
        $this->app->bind('url', function() {
            return new UrlGenerator(app('router')->getRoutes(), app('request'));
        });

        /**
         * 设定静态资源版本号
         */
        app('url')->setVersion(env('ASSET_VERSION', 0));

        /**
         * 修复 MYSQL 5.7 JSON类型识别问题
         */
        // \DB::getDoctrineSchemaManager()
            // ->getDatabasePlatform()
            // ->registerDoctrineTypeMapping('json', 'text');
    }
}
