<?php
namespace App\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WxLoginService
{
    private $wx_login_url = 'https://api.weixin.qq.com/sns/jscode2session';
    private $wx_access_token_url = 'https://api.weixin.qq.com/cgi-bin/token';

    public function getLoginSession($params){
        if(!$params){
            return 0;
        }
        $url = $this->wx_login_url . '?' . http_build_query($params);

        $client = new Client();
        $response = $client->get($url);
        if($response->getStatusCode() == '200'){
            $data = $response->getBody();
            $data = json_decode($data, true);
            return $data;
        }else{
            return 0;
        }
    }

    public function getWxAccessToken(){
        $appid = env('WX_APPID');
        $redisKey = 'wx_access_token_' . $appid;
        $token = Redis::get($redisKey);
        if($token){
            return $token;
        }else{
            $query = [
                'grant_type' => 'client_credential',
                'appid'     => $appid,
                'secret'    => env('WX_SECRET')
            ];
            $url = $this->wx_access_token_url . '?' . http_build_query($query);
            $client = new Client();
            $response = $client->get($url);
            if($response->getStatusCode() == '200'){
                $data = $response->getBody();
                $data = json_decode($data, true);
                if(isset($data['access_token'])){
                    Redis::set($redisKey, $data['access_token']);
                    Redis::expire($redisKey, $data['expires_in']);
                    return $data['access_token'];
                }else{
                    Log::error('获取access_token失败' . $data['errmsg']);
                    return 0;
                }
            }else{
                return 0;
            }
        }


    }

}