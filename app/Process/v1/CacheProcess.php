<?php

namespace App\Process\v1;

final class CacheProcess
{
    const VERSION = 'v1'; // 版本

    /**
     * 获取 缓存key
     *
     * @param $name
     * @param array $params
     * @return string
     */
    static public function getKey($name, $params = [])
    {
        $name = config('cache_setting.' . self::VERSION . '.keys' . $name, self::VERSION . ':' . $name);
        if ($params) {
//            sort($params);
//            dd($params);
            foreach ($params as $k => $v) {
                $name .= ':' . $k . ':' . $v;
            }
        }
        return $name;
    }

    /**
     * getMinutes 获取缓存时间
     * @param string $type
     * @return mixed
     *
     * @author KuanZhang
     * @date 2019/9/25 14:53
     */
    static public function getMinutes($type = 'default')
    {
        return config('cache_setting.' . self::VERSION . '.minutes.' . $type, 10);
    }

}