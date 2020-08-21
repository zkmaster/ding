<?php

namespace App\Console\Commands\V1\Home;

use App\Process\v1\HomeProcess;
use Illuminate\Console\Command;

class Body extends Command
{
    /**
     * 命令行名称
     *
     * @var string
     */
    protected $signature = 'v1.home.body {params}';

    /**
     * 命令行的描述
     * @var string
     */
    protected $description = '获取主体数据';

    /**
     * 运行
     *
     * @return mixed
     */
    public function handle()
    {
        $args = $this->arguments();
        $params = command_params_decode($args['params']);
        HomeProcess::getInstance()->getBodyData($params);
    }
}