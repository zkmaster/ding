<?php

namespace App\Http\Controllers;

use Cache;
use Log;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Helper\Token;

class ExampleController extends Controller
{
    public function login(Request $request)
    {
        $token = Token::encode(1);
        return response()->json(['token' => $token]);
    }

    public function userInfo()
    {
        $res = Token::authorization();
        return response()->json(['res' => $res]);

    }

    public function redis()
    {
        for ($i = 0; $i < 10; $i++){
            $this->putCache('wx_user_info');
        }
        dd(Cache::get('wx_user_info'));
    }

    protected function putCache($key)
    {
        $my_code = md5(uniqid(rand(), true));
        Cache::put($key, $my_code, 3600);
    }

    public function test(Request $request)
    {
        $this->validate($request, [
            'time' => 'required|int',
            'day' => 'required|int',
        ]);
        $day = $request->post('day');
        $time = $request->post('time');

        $res = $this->getEndTime($day, $time);
        return response()->json(['data' => $res]);
    }


    /**
     * 获取N个工作日后时间戳
     *
     * @param int           $day        工作日天数
     * @param int|null      $time       开始时间
     * @param array|null    $holiday    假期
     * @return int                      时间戳
     */
    public function getEndTime(int $day, int $time = null, array $holiday = [])
    {
        $time = isset($time) ? $time : time();

        $time= $this->getStartTime($time, $holiday);
        $res = (int)($time + 86400 * $day);
        for ($i = 0, $n = 0; $n <= $day && $i < 100; $i++) {
            $check_time = (int)($time + 86400 * $i); // 为0时：验证今天
            // 跳过周末
            if (!in_array(date('N', $check_time), [0, 6, 7])) {
                if ($holiday && is_array($holiday)) {
                    // 跳过假期
                    if (!in_array(date('Y-m-d', $check_time), $holiday)) {
                        $n++;
                        $res = $check_time;
                    }
                }else {
                    $n++;
                    $res = $check_time;
                }
            }
        }
        return $res;
    }

    /**
     * 获取开始时间
     *
     * @param $time 时间
     * @param array $holiday 节假日
     * @return mixed
     */
    public function getStartTime($time, $holiday = [])
    {
        if (in_array(date('N', $time), [0, 6, 7]) || in_array(date('Y-m-d', $time), $holiday)) {
            return $this->getStartTime(strtotime(date('Y-m-d', $time + 86400)), $holiday);
        }else {
            return $time;
        }
    }

    public function getIp(Request $request){
        dd($request);
    }


}
