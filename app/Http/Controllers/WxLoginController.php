<?php

namespace App\Http\Controllers;

use App\Service\WxLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class WxLoginController extends Controller
{
    private $authorization_code = 'haha_test';
    private $wx_redis_session_prefix = 'wx_session:';
    private $wx_redis_session_expire = 60 * 60 * 24;

    public function getSessionKey(Request $request, $code){

        $appid = env('WX_APPID');
        $secret = env('WX_SECRET');

        if(!$appid || !$secret){
            return $this->return_json('error', '参数错误');
        }

        $params = [
            'appid'         => $appid,
            'secret'        => $secret,
            'js_code'       => $code,
            'grant_type'    => $this->authorization_code
        ];

        $wxService = new WxLoginService();

        $response = $wxService->getSessionKey($params);

        if(!$response || isset($response['errcode'])){
            $msg = isset($response['errmsg']) ? $response['errmsg'] : '获取WX session失败';
            return $this->return_json('error', $msg);
        }

        $openid = $response['openid'];
        $session_key = $response['session_key'];
        $expires_in = $response['expires_in'];
        $sessionId = Str::random(40);





        var_dump($sessionId);




    }
}
