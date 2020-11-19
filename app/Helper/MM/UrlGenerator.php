<?php
/**
 * Created by PhpStorm.
 * User: mixmedia
 * Date: 2017/3/31
 * Time: 18:46
 */

namespace App\Helper\MM;


use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;

class UrlGenerator extends \Illuminate\Routing\UrlGenerator
{

    static protected
        $version = 0;

    protected
        $in_https = false,
        $cdn_config;

    public function __construct(RouteCollection $routes, Request $request)
    {
        parent::__construct($routes, $request); // TODO: Change the autogenerated stub

        if (\Arr::get($_SERVER, 'HTTP_X_FORWARDED_PROTO') == 'https' ||
            (\Arr::get($_SERVER, 'HTTPS') && \Arr::get($_SERVER, 'HTTPS') != 'off')) {
            $this->in_https = true;
            request()->server->set('HTTPS', 'on');
        }


        $this->cdn_config = config('cdn', []);
    }


    public function setVersion($version)
    {
        self::$version = $version;
    }

    public function getVersion()
    {
        return self::$version ? self::$version : false;
    }

    public function asset($path, $secure = null)
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        if (is_null($secure)) {
            $secure = $this->in_https;
        }


        // Once we get the root URL, we will check to see if it contains an index.php
        // file in the paths. If it does, we will remove it since it is not needed
        // for asset paths, but only for routes to endpoints in the application.
        $root = $this->formatRoot($this->formatScheme($secure));
        if (\Arr::get($this->cdn_config, 'enable')) {
            $root = $this->formatRoot($this->formatScheme($secure), 'http://' . \Arr::get($this->cdn_config, 'domain'));
        }


        return $this->removeIndex($root) . '/' . trim($path, '/') . (self::$version ? '?v=' . self::$version : '');
    }

    public function to($path, $extra = [], $secure = null)
    {
        if (is_null($secure)) {
            $secure = $this->in_https;
        }

        return parent::to($path, $extra, $secure); // TODO: Change the autogenerated stub
    }

    public function route($name, $parameters = [], $absolute = true)
    {
        $lang = empty($parameters['front_language']) ? \Lang::locale() : $parameters['front_language'];

        if (!\Request::is('admin/*', 'admin') && !is_null($this->routes->getByName('i18n.' . ltrim($name)))) {
            $parameters = array_merge($parameters, [
                'front_language' => $lang,
            ]);
            return parent::route('i18n.' . $name, $parameters, $absolute);
        }
        return parent::route($name, $parameters, $absolute); // TODO: Change the autogenerated stub
    }
}