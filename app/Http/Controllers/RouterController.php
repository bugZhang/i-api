<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class RouterController extends Controller
{

    public function getRouter(Request $request){
        $myEquipments = [
            'AC:C1:EE:7B:8E:D9' => 'Jerry Mi Note2',
            'D0:BA:E4:63:6D:97' => '插排',
            '80:C5:F2:6F:99:79' => '电视盒子',
            'BC:9F:EF:7C:06:6C' => '马大帅的Iphone',
            '60:F8:1D:B4:C2:C8' => 'Jerry Mac'
        ];

        ini_set('xdebug.default_enable', 0);
        $statusInfo = $request->getContent();
        if(!$statusInfo){
            return $this->return_json('error');
        }
        $equipments = json_decode($statusInfo);
        $newOnline = [];
        $newOffline = [];

        if($equipments && $equipments->host){
            $equipmentsCache = $this->getRouteCache();

            foreach ($equipments->host as $equipment){
                $isNew = 1;
                if ($equipmentsCache){
                    foreach ($equipmentsCache as $key => $equipmentCache){
                        if($equipment->mac == $equipmentCache->mac){
                            $isNew = 0;
                            unset($equipmentsCache[$key]);
                        }
                    }
                }
                if($isNew){
                    $newOnline[] = $equipment;
                }

            }
            if(!empty($equipmentsCache)){
                $newOffline = $equipmentsCache;
            }
            $this->setRouteCache($equipments->host);
        }

        $msg = "`--刚刚上线的设备--";
        if($newOnline){
            foreach ($newOnline as $equiment){
                $msg .= isset($myEquipments[$equiment->mac]) ? $myEquipments[$equiment->mac] : $equiment->mac . '|';
            }
        }

        $msg = $msg ? $msg . '    --刚刚离线的设备--' : '--刚刚离线的设备--';
        if($newOffline){
            foreach ($newOffline as $equiment){
                $msg .= isset($myEquipments[$equiment->mac]) ? $myEquipments[$equiment->mac] : $equiment->mac . '|';
            }
        }

        $msg .= '`';
        var_dump($msg);

        if($newOnline || $newOffline){
            $this->sendWxMsg($msg);
        }

    }

    private function sendWxMsg($msg){
        if(!$msg){
            return 0;
        }
        $key = env('MY_SEND_KEY');
        $url = 'https://pushbear.ftqq.com/sub';
        $params = [
            'sendkey'   => $key,
            'text'      => 'Wifi状态通知',
            'desp'      => $msg
        ];

//        $client = new Client();
//        $res = $client->request('POST', $url, ['json' => $params]);
//        $res = $client->post($url, $params);



//        $jsonData =json_encode($params);// response()->json($data);

        $ch = curl_init( $url );
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-type: application/x-www-form-urlencoded; charset=UTF-8') ,
            CURLOPT_POSTFIELDS => http_build_query($params)
        );
        curl_setopt_array( $ch, $options );
        $result =  curl_exec($ch);
        var_dump($result);

        die();
    }

    private function getRouteCache(){
        $redisPrefix = 'my_router_status';
        $equipments = Redis::get($redisPrefix);
        return $equipments ? unserialize($equipments) : 0;
    }

    private function setRouteCache($equipments){
        if(!$equipments){
            return 0;
        }
        $redisPrefix = 'my_router_status';
        Redis::del($redisPrefix);


        return Redis::set($redisPrefix, serialize($equipments));
    }
}