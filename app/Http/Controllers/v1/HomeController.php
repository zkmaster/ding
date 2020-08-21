<?php

namespace App\Http\Controllers\v1;

use App\Helper\Token;
use App\Process\v1\HomeProcess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $process;
    public function __construct()
    {
        $this->process = HomeProcess::getInstance();
    }

    public function index(Request $request)
    {
        $params = [
            'uid' => Token::uid()
        ];
        $this->process->getHomeData($params);
        $data = [];
        $data['top'] = $this->process->getTopData($params);
        $data['body'] = $this->process->getBodyData($params);
        return show_success($data);
    }

    public function Data()
    {

    }
}