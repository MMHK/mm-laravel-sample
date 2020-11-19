<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2020/11/19
 * Time: 15:17
 */

namespace App\Http\Controllers;


class HomeController extends Controller
{

    public function home() {
        return view('default');
    }
}