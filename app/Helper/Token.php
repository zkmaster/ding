<?php

namespace App\Helper;

use App\User;
use \DomainException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class Token
{
    /**
     * @var array 算法
     */
    public static $supported_algs = [
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'HS384' => array('hash_hmac', 'SHA384'),
        'RS256' => array('openssl', 'SHA256'),
    ];

    /**
     * 获取当前登录用户ID
     * @return bool|mixed
     *
     * @throws  \Illuminate\Http\Exceptions\HttpResponseException
     */
    public static function uid()
    {
        $token = app('request')->header('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if (config('app.debug')) {
            Log::debug('Authorization', ['token' => $token]);
        }
        $user_info = self::decode($token);
        if (is_object($user_info) && !empty($user_info->uid)){
            $user = User::find($user_info->uid);
            if ($user)
                Auth::setUser($user);
            else
                self::throwHttpResponseException(402);
            return $user_info->uid;
        }
        self::throwHttpResponseException();
    }

    /**
     * 是否已经登录
     * @return bool
     */
    public static function isLogin()
    {
        return is_null(Auth::id()) ? false : true;
    }

    /**
     * 抛出异常
     * @param int $code
     * @param string $msg
     */
    public static function throwHttpResponseException($code = 401, $msg = '请登录后重试!')
    {
        $response = new JsonResponse(['msg' => $msg], $code);
        throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
    }

    /**
     * 解析Token
     * @param $token
     * @return bool|mixed|string
     */
    public static function decode($token)
    {
        // 获取加密盐
        $secret = config('token.secret');
        // 没有配置加密盐报错
        if (empty($secret)) return false;

        $allowed_alg = [config('token.alg')];
        // 没有配置加密方式报错
        if (empty($allowed_alg)) return false;

        $tks = explode('.', $token);

        if (count($tks) != 3) return false;

        @list($head_b64, $body_b64, $crypto_b64) = $tks;

        if (null === $header = self::jsonDecode(self::urlSafeB64Decode($head_b64))) return false;

        if (null === $body = self::jsonDecode(self::urlSafeB64Decode($body_b64))) return false;

        // 用户ID格式不正确验证失败
        if(empty($body->uid) || !is_numeric($body->uid)) return false;

        if($header->ver != Header::getVersion()) return false;

        if($header->platform != Header::getUserAgent('platform')) return false;

        if($header->alg != config('token.alg')) return false;

        $time = time();
        // 签发时间大于当前服务器时间验证失败
        if($body->sta > $time) return false;

        // 过期时间小于当前服务器时间验证失败
        if($body->exp < $time){
            return false;
        }else {
            if (config('token.refresh') && $time < $body->exp + config('token.refresh_ttl')) {
                // 换签
                $new_token = self::encode($body->uid);
                header('Authorization:'.$new_token);
            }
        }

        $sig = self::urlsafeB64Decode($crypto_b64);
        if (!self::verify("$head_b64.$body_b64", $sig, $secret, $header->alg)) {
            return false;
        }
        return $body;
    }

    /**
     * 根据 uid 生成 Token
     * @param int $uid
     * @param string|null $version
     * @return string
     */
    public static function encode(int $uid, string $version = null)
    {
        $key = config('token.secret');
        $alg = config('token.alg');
        $ttl = config('token.ttl') * 60;
        $now = time();

        $user_info = [
            'uid' => $uid,
            'sta' => $now,
            'exp' => $now + $ttl,
        ];


        $header = ['alg' => $alg];

        $header['ver'] = self::setVersion($user_info['uid'], $version);
        $header['platform'] = self::setPlatform($user_info['uid']);

        $segments = [
            self::urlSafeB64Encode(self::jsonEncode($header)),
            self::urlsafeB64Encode(self::jsonEncode($user_info))
        ];
        $signing_input = implode('.', $segments);
        $signature = self::sign($signing_input, $key, $alg);
        $segments[] = self::urlSafeB64Encode($signature);

        return implode('.', $segments);
    }

    /**
     * 验证 Token sign
     * @param string $msg
     * @param string $signature
     * @param string $key
     * @param string $alg
     * @return bool
     */
    private static function verify(string $msg, string $signature, string $key, string $alg)
    {
        if (empty(self::$supported_algs[$alg])) {
            return false;
        }

        list($function, $algorithm) = self::$supported_algs[$alg];
        switch ($function) {
            case 'openssl':
                $success = openssl_verify($msg, $signature, $key, $algorithm);
                if (!$success) {
                    return false;
                } else {
                    return true;
                }
            case 'hash_hmac':
                $hash = hash_hmac($algorithm, $msg, $key, true);
                if (function_exists('hash_equals')) {
                    return hash_equals($signature, $hash);
                }
                $len = min(self::safeStrlen($signature), self::safeStrlen($hash));

                $status = 0;
                for ($i = 0; $i < $len; $i++) {
                    $status |= (ord($signature[$i]) ^ ord($hash[$i]));
                }
                $status |= (self::safeStrlen($signature) ^ self::safeStrlen($hash));
                return ($status === 0);
            default:
                return false;
        }
    }

    /**
     * 设置 Platform
     * @param $uid
     * @return array|string
     */
    public static function setPlatform($uid)
    {
        $platform = Header::getUserAgent('platform');
        $key = "platform:{$uid}";
        Cache::put($key, $platform, config('token.ttl') + 1);
        return $platform;
    }

    /**
     * 获取 API 版本
     * @param int $uid 用户ID
     * @param string|null $version API版本
     * @return string
     */
    public static function setVersion(int $uid, string $version = null)
    {
        $version = $version ?: Header::getVersion();
        $key = "version:{$uid}";
        Cache::put($key, $version, config('token.ttl') + 1);
        return $version;
    }

    public static function checkVersion(int $uid, string $version)
    {
        $key = "version:{$uid}";
        $cache_version = Cache::get($key);
        return $version == $cache_version;
    }

    /**
     * 使用 URL-safe Base64 解码字符串
     * @param string $input
     * @return bool|string
     */
    public static function urlSafeB64Decode(string $input)
    {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * 使用 URL-safe Base64 编码字符串
     * @param string $input
     * @return mixed
     */
    public static function urlSafeB64Encode(string $input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    /**
     * 解码一个字符串为PHP数组
     * @param string $input
     * @return bool|mixed
     */
    public static function jsonDecode(string $input)
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=') && !(defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints = preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
            $obj = json_decode($json_without_bigints);
        }

        if (function_exists('json_last_error') && $errno = json_last_error()) {
            return false;
        } elseif ($obj === null && $input !== 'null') {
            return false;
        }
        return $obj;
    }

    /**
     * 编码一个PHP数组为字符串
     * @param $input
     * @return bool|false|string
     */
    public static function jsonEncode(array $input)
    {
        $json = json_encode($input);
        if (function_exists('json_last_error') && $errno = json_last_error()) {
            return false;
        } elseif ($json === 'null' && $input !== null) {
            return false;
        }
        return $json;
    }

    /**
     * 用给定的 秘钥 和 算法 对 字符串 签名。
     * @param string $msg
     * @param string $key
     * @param string $alg
     *
     * @return string An encrypted message
     *
     * @throws DomainException Unsupported algorithm was specified
     */
    public static function sign(string $msg, string $key, string $alg = 'HS256')
    {
        if (empty(self::$supported_algs[$alg])) {
            throw new DomainException('Not Found This Alg : ' . $alg);
        }
        list($function, $algorithm) = self::$supported_algs[$alg];
        switch ($function) {
            case 'hash_hmac':
                return hash_hmac($algorithm, $msg, $key, true);
            case 'openssl':
                $signature = '';
                $success = openssl_sign($msg, $signature, $key, $algorithm);
                if (!$success) {
                    return false;
                } else {
                    return $signature;
                }
        }
    }
}
