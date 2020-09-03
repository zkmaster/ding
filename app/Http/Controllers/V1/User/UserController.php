<?php


namespace App\Http\Controllers\V1\User;


use App\Http\Controllers\V1\VersionBaseController;
use Dingo\Api\Http\Request;

class UserController extends VersionBaseController
{
    public function getInfo(Request $request)
    {
        encrypt($request->get('id'));
        $this->showError('字段验证失败');
    }
}
