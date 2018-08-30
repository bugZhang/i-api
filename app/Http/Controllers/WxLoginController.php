<?php

namespace App\Http\Controllers;

use App\Model\WxUserModel;
use App\Service\WxLoginService;
use App\Utils\WxBizDataCrypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class WxLoginController extends Controller
{
    private $authorization_code = 'authorization_code';

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
            Log::error('保存用户信息失败' . explode(',', $userParams));
            return $this->return_json('error', '保存失败');
        }

    }

    public function getMyZhi(Request $request){

        $t = $request->input('t');
        if($t != 'b'){    //如果是b 则不显示
            $seed = time() % 3;
            if($seed == 1){
                return $this->return_json('success', ['zhi'=>'7GVKjI10Dk']);
            }elseif ($seed == 2){
                return $this->return_json('success', ['zhi'=>'7GVKjI10Dk']);
            }else{
                // return $this->return_json('success', ['zhi'=>'7GVKjI10Dk']);
                return $this->return_json('error', ['data'=>'测试测试']);
            }
        }else{
            return $this->return_json('error', ['data'=>'测试测试']);
        }

    }

    public function checkPsid(Request $request){
        if($request->wx_openid){
            Log::warning('检查PSID 登陆OK');
            return $this->return_json('success', 'OK');
        }else{
            Log::warning('检查PSID 登陆已经过期了');
            return $this->return_json('nologin', 'expire');
        }

    }

}
