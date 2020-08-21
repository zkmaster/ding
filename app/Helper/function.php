<?php

if (! function_exists('curl_request')) {
    /**
     * CURL Request
     */
    function curl_request($api, $method = 'GET', $params = array(), $headers = [])
    {
        $curl = curl_init();

        switch (strtoupper($method)) {
            case 'GET':
                if (!empty($params)) {
                    $api .= (strpos($api, '?') ? '&' : '?') . http_build_query($params);
                }
                curl_setopt($curl, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
        }

        curl_setopt($curl, CURLOPT_URL, $api);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);

        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            return false;
        }

        curl_close($curl);

        return $response;
    }
}

if (! function_exists('show_error')) {
    /**
     * Show Error With Http_code
     */
    function show_error($code, $message)
    {
        $response = response()->json([
            'status_code' => $code,
            'error_desc' => $message
        ]);
//        ->setStatusCode($code);
        $response->header('X-'.config('app.name').'-ErrorCode', $code);
        $response->header('X-'.config('app.name').'-ErrorDesc', urlencode($message));
        return $response;
    }
}
if (! function_exists('show_success')) {

    function show_success($data)
    {
        $response = response()->json([
            'status_code' => 200,
            'message' => null,
            'data' => $data
        ]);
        $response->header('X-'.config('app.name').'-ErrorCode', 200);
        $response->header('X-'.config('app.name').'-ErrorDesc', null);
        return $response;
    }
}

if (! function_exists('get_encrypt_password')) {
    /**
     * 获取加密后的字符串
     *
     * @param string $string 目标字符串
     * @param string|null $salt 加密盐
     * @return string 加密后的字符串
     */
    function get_encrypt_password(string $string, string $salt = null)
    {
        return md5(md5($string) . $salt);
    }
}

if (! function_exists('command_params_decode')) {
    /**
     * 解析特定字符串为一级数组 （由command_params_incode加密）
     * @param string   $str   由command_params_incode加密字符串
     * @param string   $code1 一级间隔符
     * @param string   $code2 二级间隔符
     * @return array
     */
    function command_params_decode ($str, $code1=':', $code2=',')
    {
        $arr = explode($code1, $str);
        if (!$arr || count($arr) != 2) return [];
        if (count(explode($code2, $arr[0])) != count(explode($code2, $arr[1]))) return [];
        return array_combine(explode($code2, $arr[0]), explode($code2, $arr[1]));
    }
}

if (! function_exists('command_params_incode')) {
    /**
     * 加密一级索引数组为字符串
     * @param array    $arr   一级（key=>value）数组
     * @param string   $code1 一级间隔符
     * @param string   $code2 二级间隔符
     * @return string
     */
    function command_params_incode ($arr, $code1=':', $code2=',')
    {
        return implode($code2,array_keys($arr)) . $code1 . implode($code2,array_values($arr));
    }
}


