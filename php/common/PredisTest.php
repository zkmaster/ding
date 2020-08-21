<?php

/**
 * 测试Predis连接
 */

require_once('../../vendor/autoload.php');

use Predis\Client as RedisClient;

echo "测试 Predis 开始 \n";

$redis_config = [
    'host' => '127.0.0.1',
    'port' => '6379'
];

$client = new RedisClient($redis_config, [ 'prefix' => 'ding:']);

$client->set('test_key', 'test');

$ttl = $client->ttl('test_key');

echo "Predis 测试结束！\n";




