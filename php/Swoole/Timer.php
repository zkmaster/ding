<?php

/**
 * 定时器
 * OK
 */

//每隔1000ms触发一次
$timer_tick_id = swoole_timer_tick(1000, function ($timer_id) {
    echo "tick-{$timer_id}-2000ms\n";
});

//5000 ms后执行此函数
swoole_timer_after(5000, function () use ($timer_tick_id) {
    echo "after 5000ms.\n";
    // 关闭定时器
    swoole_timer_clear($timer_tick_id);
    echo "已关闭 定时器：$timer_tick_id \n";
});