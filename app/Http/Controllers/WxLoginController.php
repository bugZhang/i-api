<?php

namespace App\Http\Controllers;

use App\Service\WxLoginService;

class WxLoginController extends Controller
{

    public function getSessionKey($code){
        if(!$code){
            return $this->return_json('error', '参数错误');
        }

        $secret = env('WX_SECRET');

        $params = [
            'appid'         => 'wxdef87a6c952d8727',
            'secret'        => $secret,
            'js_code'       => $code,
            'grant_type'    => 'asdaaafffafs'
        ];

        $wxService = new WxLoginService();
        $response = $wxService->getLoginSession($params);

        if($response && !isset($response['errcode'])){
            return $this->return_json('success', $response);
        }else{
            $errMsg = $response && isset($response['errmsg']) ? $response['errmsg'] : '获取失败';
            return $this->return_json('error', $errMsg);
        }

    }

}
