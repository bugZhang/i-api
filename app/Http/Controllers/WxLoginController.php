<?php

namespace App\Http\Controllers;

use App\Model\WxUserModel;
use App\Service\WxLoginService;
use App\Utils\WxBizDataCrypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class WxLoginController extends Controller
{
    private $authorization_code = 'haha_test';
    private $wx_redis_session_prefix = 'wx_session:';
    private $wx_redis_session_expire = 60 * 60 * 24;

    public function getSessionKey(Request $request){

        $code   = $request->input('code');
        $sex    = $request->input('sex',0);
        $province   = $request->input('province', '');
        $city       = $request->input('city', '');
        $phone      = $request->input('phone', '');

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

        $response = $wxService->getLoginSession($params);

        if(!$response || isset($response['errcode'])){
            $msg = isset($response['errmsg']) ? $response['errmsg'] : '获取WX session失败';
            return $this->return_json('error', $msg);
        }

        $expires_in = $response['expires_in'] ? $response['expires_in'] * 2 : $this->wx_redis_session_expire;
        $sessionId = Str::random(40);

        $hKey = $this->wx_redis_session_prefix . $sessionId;

        Redis::hset($hKey, 'openid', $response['openid']);
        Redis::hset($hKey, 'session_key', $response['session_key']);
        Redis::expire($hKey, $expires_in);

        $userModel  = new WxUserModel();
        $user = [
            'open_id'   => $response['openid'],
            'sex'       => $sex,
            'province'  => $province,
            'city'      => $city,
            'phone'     => $phone
        ];
        $userModel->saveOrUpdate($user);

        return $this->return_json('success', ['sid'=>$sessionId]);
    }

    public function getUser($openid){
//        $userModel  = new WxUserModel();
//
//        $params = [
//            'open_id' => 'asdf',
//            'sex'       => 'male',
//            'province' => 'shangdong',
//            'city'  => 'qingdao'
//        ];
//        $userModel->saveOrUpdate($params);


        $utils = new WxBizDataCrypt();


        $appid = 'wx4f4bc4dec97d474b';
        $sessionKey = 'tiihtNczf5v6AKRyjwEUhQ==';

        $encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';

        $utils->WXBizDataCrypt($appid, $sessionKey);

        $errCode = $utils->decryptData($encryptedData, $iv, $data );

        var_dump($data);


    }



}
