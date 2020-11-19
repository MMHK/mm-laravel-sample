<?php

use App\Helper\Http\HttpRequest;
use App\Helper\Sms\Contracts\GatewayInterface;
use App\Helper\Sms\Contracts\MessageInterface;
use Illuminate\Container\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if (!defined('ENV_DEV')) {
    define('ENV_DEV', 'local');
}
if (!defined('ENV_TEST')) {
    define('ENV_TEST', 'testing');
}
if (!defined('ENV_PRO')) {
    define('ENV_PRO', 'production');
}

if (!function_exists('dump')) {
    function dump($params)
    {
        if (is_array($params) || var_export($params)) {
            var_export($params);
        } else {
            var_dump($params);
        }
    }
}

if (!function_exists('relative_path_from_public')) {
    /**
     * 将public文件的绝对路径替换为相对路径
     * 如果文件不在public目录下就会返回false
     * @param string $path 文件的绝对物理路径
     * @return bool|mixed
     */
    function relative_path_from_public($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $public_path = str_replace(DIRECTORY_SEPARATOR, '/', public_path());
        if (strpos($path, $public_path) !== false) {
            return str_replace($public_path, '', $path);
        }

        return false;
    }
}

if (!function_exists('running_on_windows')) {
    /**
     * 检测是否在windows上运行
     * @return bool
     */
    function running_on_windows()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }
}


/**
 * 模板替換
 */
if (!function_exists('template_replace_args')) {
    function template_replace_args($template, $args, $is_recursion = false)
    {
        //搜索$args中的key，替换掉
        foreach ($args as $key => $row) {
            //递归处理嵌套的数组数据
            if (is_array($row)) {
                $template = template_replace_args($template, $row, true);
                continue;
            }
            $template = str_replace('{:' . $key . '}', $row, $template);
        }
        //递归模式下忽略其他
        if (!$is_recursion) {
            //干掉其他漏网之鱼
            $template = preg_replace('/{:([^}]+)}/i', '', $template);
        }
        return $template;
    }
}


/**
 * 数字格式化
 */
if(!function_exists('mm_number_format')){
    function mm_number_format($number , int $decimals = 2 , string $dec_point = '.' , string $thousands_sep = ',' ){
        if(is_numeric($number)){
            return number_format($number, $decimals, $dec_point, $thousands_sep);
        }
        return '';
    }
}

if (!function_exists('array_max')) {
    function array_max($arr, $key = '')
    {
        if ($key)
            return collect($arr)->max($key);
        return collect($arr)->max();
    }
}


if (!function_exists('start_time')) {
    function start_time()
    {
        return explode(' ', microtime());
    }
}

if (!function_exists('end_time')) {
    function end_time(array $start_time)
    {
        if (!$start_time)
            return null;
        $end_time = explode(' ', microtime());
        return round(array_sum($end_time) - (array_sum($start_time)), 3);
    }
}


if (!function_exists('inWeChat')) {
    /**
     * 判定是否在微信浏览器环境
     * @return bool
     */
    function inWeChat(){
        if ( strpos(\Arr::get($_SERVER, 'HTTP_USER_AGENT'), 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }
}

if (!function_exists('hash_route')) {
    function hash_route($name, array $params) {
        $hash = encrypt($params);

        return route($name, ['hash' => $hash]);
    }
}

if (!function_exists('url_safe_string')) {
    function url_safe_string($src) {
        //convert fr to ascii
        $src = iconv('UTF-8', 'ASCII//TRANSLIT', $src);
        $src = trim(strtolower($src));
        $src = preg_replace('/[^a-z0-9\\s\-]+/i',
            ' ',
            $src);
        return preg_replace('/[\\s]+/i',
            '-',
            $src);
    }
}
if (!function_exists('html_linebreak')) {
    function html_linebreak($src) {
        return str_replace("\n", '<br>', $src);
    }
}

if (!function_exists('number_fix')) {
    function number_fix($number, $length) {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('__term')) {
    /**
     * 翻译分类名称
     */
    function __term($name) {
        $key = 'terms.'.$name;
        return \Lang::has($key) ? \Lang::get($key) : $name;
    }
}

if (!function_exists('__g')) {
    /**
     * 翻译分类名称
     */
    function __g($name, $group) {
        $key = $group.'.'.$name;
        return \Lang::has($key) ? \Lang::get($key) : $name;
    }
}

if (!function_exists('banner_url')) {
    function banner_url($url) {
        return app('\App\Services\i18nService')
            ->bannerURL($url);
    }
}

if (!function_exists('mm_money_format')) {
    function mm_money_format($val) {
        if (!is_numeric($val)) {
            return $val;
        }

        if (($val * 100) % 100 == 0) {
            return number_format($val);
        }

        return number_format($val, 2);
    }
}

if (!function_exists('month_convert')) {
    function month_convert($month)
    {
        if (\Lang::getLocale() == 'zh') {
            return $month . '月';
        }

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];

        return \Arr::get($months, $month - 1, $month);
    }
}


if (!function_exists('get_chinese_weekday')) {
    function get_chinese_weekday($timestamp)
    {
        $weekday = date('w', $timestamp);

        $week_list = ['日', '一', '二', '三', '四', '五', '六'];

        return '(星期' . $week_list[$weekday] . ')';
    }
}

/**
 * array helper function polyfill
 */

if (! function_exists('array_add')) {
    /**
     * Add an element to an array using "dot" notation if it doesn't exist.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     *
     * @deprecated Arr::add() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_add($array, $key, $value)
    {
        return Arr::add($array, $key, $value);
    }
}

if (! function_exists('array_collapse')) {
    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array  $array
     * @return array
     *
     * @deprecated Arr::collapse() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_collapse($array)
    {
        return Arr::collapse($array);
    }
}

if (! function_exists('array_divide')) {
    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array  $array
     * @return array
     *
     * @deprecated Arr::divide() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_divide($array)
    {
        return Arr::divide($array);
    }
}

if (! function_exists('array_dot')) {
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     *
     * @deprecated Arr::dot() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_dot($array, $prepend = '')
    {
        return Arr::dot($array, $prepend);
    }
}

if (! function_exists('array_except')) {
    /**
     * Get all of the given array except for a specified array of keys.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     *
     * @deprecated Arr::except() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_except($array, $keys)
    {
        return Arr::except($array, $keys);
    }
}

if (! function_exists('array_first')) {
    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     *
     * @deprecated Arr::first() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_first($array, callable $callback = null, $default = null)
    {
        return Arr::first($array, $callback, $default);
    }
}

if (! function_exists('array_flatten')) {
    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     *
     * @deprecated Arr::flatten() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_flatten($array, $depth = INF)
    {
        return Arr::flatten($array, $depth);
    }
}

if (! function_exists('array_forget')) {
    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return void
     *
     * @deprecated Arr::forget() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_forget(&$array, $keys)
    {
        return Arr::forget($array, $keys);
    }
}

if (! function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     *
     * @deprecated Arr::get() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_get($array, $key, $default = null)
    {
        return Arr::get($array, $key, $default);
    }
}

if (! function_exists('array_has')) {
    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     *
     * @deprecated Arr::has() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_has($array, $keys)
    {
        return Arr::has($array, $keys);
    }
}

if (! function_exists('array_last')) {
    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     *
     * @deprecated Arr::last() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_last($array, callable $callback = null, $default = null)
    {
        return Arr::last($array, $callback, $default);
    }
}

if (! function_exists('array_only')) {
    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     *
     * @deprecated Arr::only() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_only($array, $keys)
    {
        return Arr::only($array, $keys);
    }
}

if (! function_exists('array_pluck')) {
    /**
     * Pluck an array of values from an array.
     *
     * @param  array   $array
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     *
     * @deprecated Arr::pluck() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_pluck($array, $value, $key = null)
    {
        return Arr::pluck($array, $value, $key);
    }
}

if (! function_exists('array_prepend')) {
    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  mixed  $key
     * @return array
     *
     * @deprecated Arr::prepend() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_prepend($array, $value, $key = null)
    {
        return Arr::prepend($array, $value, $key);
    }
}

if (! function_exists('array_pull')) {
    /**
     * Get a value from the array, and remove it.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     *
     * @deprecated Arr::pull() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_pull(&$array, $key, $default = null)
    {
        return Arr::pull($array, $key, $default);
    }
}

if (! function_exists('array_random')) {
    /**
     * Get a random value from an array.
     *
     * @param  array  $array
     * @param  int|null  $num
     * @return mixed
     *
     * @deprecated Arr::random() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_random($array, $num = null)
    {
        return Arr::random($array, $num);
    }
}

if (! function_exists('array_set')) {
    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     *
     * @deprecated Arr::set() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_set(&$array, $key, $value)
    {
        return Arr::set($array, $key, $value);
    }
}

if (! function_exists('array_sort')) {
    /**
     * Sort the array by the given callback or attribute name.
     *
     * @param  array  $array
     * @param  callable|string|null  $callback
     * @return array
     *
     * @deprecated Arr::sort() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_sort($array, $callback = null)
    {
        return Arr::sort($array, $callback);
    }
}

if (! function_exists('array_sort_recursive')) {
    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     * @return array
     *
     * @deprecated Arr::sortRecursive() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_sort_recursive($array)
    {
        return Arr::sortRecursive($array);
    }
}

if (! function_exists('array_where')) {
    /**
     * Filter the array using the given callback.
     *
     * @param  array  $array
     * @param  callable  $callback
     * @return array
     *
     * @deprecated Arr::where() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_where($array, callable $callback)
    {
        return Arr::where($array, $callback);
    }
}

if (! function_exists('array_wrap')) {
    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     *
     * @deprecated Arr::wrap() should be used directly instead. Will be removed in Laravel 6.0.
     */
    function array_wrap($value)
    {
        return Arr::wrap($value);
    }
}