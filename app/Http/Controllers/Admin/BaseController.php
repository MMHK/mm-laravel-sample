<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/7
 * Time: 11:54
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function __construct()
    {
        \View::share([
            'page_title' => '',
            'site_name' => config('app.name'),
            'meta' => [],
            'layout' => 'layout.admin',
        ]);
    }


}