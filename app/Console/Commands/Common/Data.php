<?php

namespace App\Console\Commands\Common;

use Illuminate\Console\Command;

class Data extends Command
{
    /**
     * 命令行名称
     *
     * @var string
     */
    protected $signature = 'data.test';

    /**
     * 命令行的描述
     * @var string
     */
    protected $description = '测试数据';

    /**
     * 运行
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
        $this->getData($arguments);
    }
}