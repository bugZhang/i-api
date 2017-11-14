<?php
namespace App\Service;

use GuzzleHttp\Client;

class WxContactService{


    private $wx_contact_send_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';

    public function sendMsg(array $msg, $access_token){
        if(!$access_token){
            return 0;
        }
        $url = $this->wx_contact_send_url . $access_token;

        $client = new Client();
        $response = $client->request('POST', $url, ['query'=>json_encode($msg)]);
        if($response->getStatusCode() == '200'){
            $data = $response->getBody();
            $data = json_decode($data, true);
            return $data;
        }else{
            return 0;
        }

    }

}