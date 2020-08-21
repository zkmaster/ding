<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Helper\Token;
use App\Model\User;
use Validator;
use Cache;
use DB;
use Log;

class   UserController extends Controller
{
    public function loginIn(Request $request)
    {
        $this->validate(
            $request,
            [
                'account' => [
                    'required',
                    'regex:/^[1]([3-9])[0-9]{9}$/'
                ],
                'password' => 'required|alpha_dash|min:6',
            ],
            [
                'account.regex' => '账号格式有误',
                'password.alpha_dash' => '密码格式有误',
                'password.min' => '密码至少为 6 个字符',
            ]
        );
        $account = $request->post('account');
        $password = $request->post('password');

        $user_model = DB::table('user')->where(['mobile' => $account])->first();
        if (is_null($user_model)) {
            return show_error(400, '账号输入错误！');
        }
        if ($user_model->status != User::NORMAL) return show_error(400, '账号被禁用！');
//        var_dump($user_model->password);
//        var_dump(get_encrypt_password($password));
        if ($user_model->password != get_encrypt_password($password, $user_model->salt))
            return show_error(400, '登陆失败！');
        $token = Token::encode($user_model->id);
        Log::info(__METHOD__);
        return response()->json(['token' => $token]);
    }

    public function userInfo(Request $request)
    {
        Token::uid();
        return response()->json(['data' => Auth::user()->toArray()]);

    }

}
