<?php

namespace App\Helper;

class Header
{
    public static function getUserAgent($key = false)
    {
        $arr = [];

        if ($ua = app('request')->header('X-' . config('app.name') . '-UserAgent')) {
            if (strpos($ua, '/') !== false) {
                $ua = strtolower($ua);
                $property = explode('/', $ua);
                $arr[$property[0]] = $property[1];
            }
        }

        if ($key) {
            return (isset($arr[$key]) && $arr[$key]) ? strtolower($arr[$key]) : '';
        }

        return null;
    }

    public static function getVersion()
    {
        $version = null;

        if ($ver = app('request')->header('X-' . config('app.name') . '-Ver')) {
            $version = $ver;
        }

        return $version;
    }
}
