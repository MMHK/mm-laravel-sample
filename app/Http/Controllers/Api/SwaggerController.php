<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/10
 * Time: 11:20
 */

namespace App\Http\Controllers\Api;


class SwaggerController extends BaseController
{

    public function urls() {
        if (\App::environment(ENV_PRO)) {
            return [];
        }

        return [
            [
                'name' => 'Front API',
                'url' => route('swagger.front.doc'),
            ],
            [
                'name' => 'Admin API',
                'url' => route('swagger.admin.doc'),
            ]
        ];
    }

    public function admin() {
        define('SWAGGER_API_URI', url('admin/api'));

        return \OpenApi\scan(app_path('Http/Controllers/Admin/Api'))->toJson();
    }

    public function front() {
        define('SWAGGER_API_URI', url('api'));

        return \OpenApi\scan(app_path('Http/Controllers/Api'))->toJson();
    }

    public function ui() {
        if (\App::environment(ENV_PRO)) {
            return 'coming soon...';
        }

        return view('swagger.ui');
    }


}