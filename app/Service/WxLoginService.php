<?php
/**
 * Created by PhpStorm.
 * User: yixina-d
 * Date: 17/9/21
 * Time: 16:40
 */

namespace App\Service;


use GuzzleHttp\Client;

class WxLoginService
{

    private $wx_login_uri = 'https://api.weixin.qq.com/sns/jscode2session';

    public function getSessionKey($params){

        if(!$params){
            return 0;
        }

        $url = $this->wx_login_uri . '?' . http_build_query($params);

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
}