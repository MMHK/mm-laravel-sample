<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/7
 * Time: 12:08
 */

namespace App\Http\Controllers\Admin;



class HomeController extends BaseController
{

    public function dashboard() {
        return view('admin.dashboard')
            ->with([
                'page_title' => 'Dashboard',
            ]);
    }

    public function demo() {
        return view('admin.demo')->with([
            'page_title' => 'Demo',
        ]);
    }
}