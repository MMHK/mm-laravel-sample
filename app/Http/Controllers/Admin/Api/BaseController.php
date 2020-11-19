<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2019/5/10
 * Time: 11:33
 */

namespace App\Http\Controllers\Admin\Api;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


class BaseController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

}