<?php
/**
 * Created by PhpStorm.
<<<<<<< HEAD
 * User: Jerry
 * Date: 2017/9/20
 * Time: 下午10:22
=======
 * User: yixina-d
 * Date: 17/9/21
 * Time: 16:40
>>>>>>> wxapi
 */

namespace App\Service;


use GuzzleHttp\Client;

class WxLoginService
{
    private $wx_login_url = 'https://api.weixin.qq.com/sns/jscode2session';

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

}