<?php

$server = new swoole_server("127.0.0.1", 9501);

// 设置异步任务的工作进程数量
$server->set(['task_worker_num' => 4]);

// 监听数据接收事件
$server->on('receive', function ($server, $fd, $from_id, $data) {
    $data = explode('::', $data);
    foreach ($data as $value) {
        // 投递异步任务
        $task_id = $server->task($value);
        echo "Dispath AsyncTask: id = $task_id \n";
    }
    echo "ALl AsyncTask End !!!\n";
});

// 处理异步任务
$server->on('task', function ($server, $task_id, $from_id, $data) {
   echo "New AsyncTask[id = $task_id] \n";
   // 返回执行异步任务的结果
   $server->finish("$data -> OK");
});

// 处理异步任务的结果
$server->on('finish', function ($server, $task_id, $data) {
    echo "AsyncTask[$task_id] Finish: $data" . PHP_EOL;
});

$server->start();

