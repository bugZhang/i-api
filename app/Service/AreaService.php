<?php
namespace App\Service;

use GuzzleHttp\Client;

class AreaService {


    private $tx_api_key = '6HHBZ-TVXHP-TRHD3-L5NUA-GTW3Q-UBF5L';

    private $tx_api_url_area_list = 'http://apis.map.qq.com/ws/district/v1/list';

    private $tx_api_url_area_get_getchildren = 'http://apis.map.qq.com/ws/district/v1/getchildren';

    public function getList(){
        $url = $this->tx_api_url_area_list . '?'. http_build_query(['key' => $this->tx_api_key]);

        $client = new Client();
        $response = $client->get($url);
        if($response->getStatusCode() == '200'){
            $data = $response->getBody();
            $data = json_decode($data, true);
            return $data['result'] ? $data['result'] : 0;
        }else{
            return 0;
        }
    }

    public function getProvince(){
        $list = $this->getList();
        if($list){
            return $list[0];
        }else{
            return 0;
        }
    }


    public function getCity($provinceId){

        if(!$provinceId){
            return 0;
        }

        $url = $this->tx_api_url_area_get_getchildren . '?' . http_build_query(['key'=>$this->tx_api_key, 'id'=>$provinceId]);

        $client = new Client();
        $response = $client->get($url);
        if($response->getStatusCode() == '200'){
            $data = $response->getBody();
            $data = json_decode($data, true);
            return $data['result'] ? $data['result'][0] : 0;
        }else{
            return 0;
        }

    }

}