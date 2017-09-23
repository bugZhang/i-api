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

    public function getSessionKey($code){

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

        $expires_in = $response['expires_in'] ? $response['expires_in'] * 2 : env('WX_REDIS_SESSION_EXPIRE');
        $sessionId = Str::random(40);

        $hKey = env('WX_REDIS_SESSION_PREFIX') . $sessionId;

        Redis::hset($hKey, 'openid', $response['openid']);
        Redis::hset($hKey, 'session_key', $response['session_key']);
        Redis::expire($hKey, $expires_in);

        return $this->return_json('success', ['sid'=>$sessionId]);
    }


    public function saveUser(Request $request){

        if(!$request->userInfo){
            return $this->return_json('error', '获取参数失败');
        }
        $encryptedData = $request->encryptedData;
        $appid = env('WX_APPID');

        $sessionId = $request->header('p-sid');
        $hKey = env('WX_REDIS_SESSION_PREFIX') . $sessionId;
        $session_key    = Redis::hget($hKey, 'session_key');

        $iv = $request->iv;

        $wxCrypt = new WxBizDataCrypt();
        $wxCrypt->WXBizDataCrypt($appid, $session_key);

        $result = $wxCrypt->decryptData($encryptedData, $iv, $userInfo);

        if($result !== 1){
            return $this->return_json('error', '数据解密失败');
        }

        $userParams = [
            'open_id'   => $userInfo->openId,
            'gender'    => $userInfo->gender,
            'country'   => $userInfo->country,
            'province'  => $userInfo->province,
            'city'      => $userInfo->city,
            'avatar_url'=> $userInfo->avatarUrl,
            'nick_name' => $userInfo->nickName,
        ];

        $userModel  = new WxUserModel();
        $status = $userModel->saveOrUpdate($userParams);
        if($status){
            return $this->return_json('success', '保存成功');
        }else{
            return $this->return_json('error', '保存失败');
        }

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
