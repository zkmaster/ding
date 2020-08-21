<?php
namespace App\Process\v1;
use Illuminate\Support\Facades\Cache;

class HomeProcess
{
    // 单例模式
    private static $instance;
    private function __construct() {}
    private function __clone() {}
    public static function getInstance() {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * getTopData 获取顶部数据
     * @param array $params
     * @return mixed
     *
     * @author KuanZhang
     * @date 2019/9/25 16:57
     */
    public function getTopData(array $params)
    {
        $cache_key = CacheProcess::getKey('home.top', $params);
        $minutes = CacheProcess::getMinutes();
        return Cache::remember($cache_key, $minutes, function () use ($params){
            sleep(3);
            return uniqid();
        });
    }

    /**
     * getBodyData 获取主体数据
     * @param array $params
     * @return mixed
     *
     * @author KuanZhang
     * @date 2019/9/25 16:57
     */
    public function getBodyData(array $params)
    {
        $cache_key = CacheProcess::getKey('home.body', $params);
        $minutes = CacheProcess::getMinutes();
        return Cache::remember($cache_key, $minutes, function () use ($params){
            sleep(3);
            return uniqid();
        });
    }

    /**
     * getHomeData 获取首页数据
     * @param $params
     *
     * @author KuanZhang
     * @date 2019/9/25 15:04
     */
    public function getHomeData($params)
    {
        $command_params = command_params_incode($params);
        $arr = [
            'cd ../ && php artisan v1.home.top ' . $command_params,
            'cd ../ && php artisan v1.home.body ' . $command_params,
        ];
        MultiProcess::run($arr);
    }

}