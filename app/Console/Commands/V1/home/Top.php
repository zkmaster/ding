<?php

namespace App\Console\Commands\V1\Home;

use Illuminate\Console\Command;
use App\Process\v1\HomeProcess;

class Top extends Command
{
    /**
     * 命令行名称
     *
     * @var string
     */
    protected $signature = 'v1.home.top {params}';

    /**
     * 命令行的描述
     * @var string
     */
    protected $description = '获取顶部数据';

    /**
     * 运行
     *
     * @return mixed
     */
    public function handle()
    {
        $args = $this->arguments();
        $params = command_params_decode($args['params']);
        HomeProcess::getInstance()->getTopData($params);
    }
}