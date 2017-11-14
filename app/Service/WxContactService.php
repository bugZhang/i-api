<?php
namespace App\Service;

use GuzzleHttp\Client;

class WxContactService{


    private $wx_contact_send_url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=';

    public function sendMsg(array $msg){
        $url = $this->wx_contact_send_url . env('WX_MSG_TOKEN');

        $client = new Client();
        $response = $client->request('POST', $url, ['jons'=>$msg]);
        if($response->getStatusCode() == '200'){
            $data = $response->getBody();
            $data = json_decode($data, true);
            return $data;
        }else{
            return 0;
        }

    }

}