<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TaobaoController extends Controller
{


    private $topClient;
    private $ad_zoneId = '128300260';

    public function __construct()
    {
        self::setTopClient();
    }

    public function test(){
        $this->getFavouriteList();
    }

    private function setTopClient(){
        if(!$this->topClient){
            $this->topClient = new \TopClient(env('TBK_APPID'), env('TBK_SECRET'));
            $this->topClient->format = 'json';
        }
    }

    public function getItems(){
        $req = new \TbkItemGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ("女装");
        $req->setPageSize("20");
        $resp = $this->topClient->execute($req);
    }

    public function sharePwd(Request $request){
        $logo = $request->input('logo');
        $title  = $request->input('title');
        $url    = $request->input('url');

//        $logo = 'http://img1.tbcdn.cn/tfscom/i2/749309273/TB2c19mzxtmpuFjSZFqXXbHFpXa_!!749309273.jpg';
//        $title = '特级无核山楂圈零食无籽去籽无糖山楂干中心圈中药材泡茶500g包邮';
//        $url = 'https://uland.taobao.com/coupon/edetail?e=NGSMQBiDUiUGQASttHIRqV4gYPzQfU52eE%2FZWGw7M20OH4xMRYRCNmH1qvx5ArxuoxtOCp1lYzWjC%2FhQv7OdIZQ5wfGz%2Fu%2BNGLN3c5IKM9MFjaZhgpTjjRlqjQc7%2B9fT';

        $pwd = $this->createPwd($url, $title, $logo);
        if($pwd){
            return $this->return_json('success', ['pwd' => $pwd]);
        }else{
            return $this->return_json('error', '生成口令码失败！');
        }

    }

    public function getFavouriteItems($favouriteId, $page){
        $favourite_items_key = 'tbk_favourite_items:' . $favouriteId . ':' . $page;

        $items = Redis::get($favourite_items_key);
$items='';
        if($items){
            $items = json_decode(unserialize($items));
            return $this->return_json('success', $items);
        }else{
            $req = new \TbkUatmFavoritesItemGetRequest();
            $req->setPlatform("2");
            $req->setPageSize("20");
            $req->setAdzoneId($this->ad_zoneId);
            $req->setUnid("wechat");
            $req->setFavoritesId($favouriteId);
            $req->setPageNo($page);
            $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,coupon_click_url,click_url,type,coupon_info,coupon_remain_count");
            $resp = $this->topClient->execute($req);
            if(!$resp && isset($resp->code)){
                return $this->return_json('error', '未查询到数据');
            }else{
                if($resp->results->uatm_tbk_item){
                    foreach ($resp->results->uatm_tbk_item as $item){
                        if(isset($item->coupon_info)){
                            $pattern = '/(?P<coupon_limit_money>\d+)(.*)(?P<coupon_money>\d+)/';
                            preg_match($pattern, $item->coupon_info, $matches);
                            if(isset($matches['coupon_money']) &&
                                isset($matches['coupon_limit_money']) &&
                                $item->zk_final_price_wap > $matches['coupon_limit_money']
                            ){
                                $item->coupon_money = $matches['coupon_money'];
                                $item->zk_final_coupon_price_wap = $item->zk_final_price_wap - $matches['coupon_limit_money'];
                            }
                        }
                    }
                }

                Redis::set($favourite_items_key, serialize(json_encode($resp->results->uatm_tbk_item, true)));
                Redis::expire($favourite_items_key, 60 * 30);
                return $this->return_json('success', $resp->results->uatm_tbk_item);
            }
        }
    }

    public function getCouponItems(){
        $req = new \TbkDgItemCouponGetRequest();
        $req->setAdzoneId($this->ad_zoneId);
        $req->setPlatform("2");
//        $req->setCat("16,18");
        $req->setPageSize("20");
        $req->setQ("富平柿饼");
        $req->setPageNo(1);
        $resp = $this->topClient->execute($req);
        var_export($resp);
    }

    private function getFavouriteList(){
        $req = new \TbkUatmFavoritesGetRequest();
        $req->setPageNo("1");
        $req->setPageSize("20");
        $req->setFields("favorites_title,favorites_id,type");
//        $req->setType("1");
        $resp = $this->topClient->execute($req);
        var_export($resp);
    }

    public function getItemInfo($id){
        $this->getItemInfoById($id);
    }
    public function queryPwdFromPwd(Request $request){
        $content = $request->input('content');
        $content = "我剁手都要买的宝贝（绿林手工热熔胶枪大号小号塑料玻璃热熔枪送热溶胶棒包邮20W-100W），快来和我一起瓜分红I包】http://a.nfi0.com/h.x8TyYx 点击链接，再选择浏览器打开；或复制这条信息￥0QEm0i3Qzmo￥后打开👉手淘👈";

        $url = $this->queryPwd($content);
        $query = parse_url($url);
        parse_str($query['query'], $params);
        var_dump($params['id']);
    }

    private function getItemInfoById($num_iids, $platform = 2){
        $req = new \TbkItemInfoGetRequest();
//        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
        $req->setPlatform($platform);
        $req->setNumIids($num_iids);
        $resp = $this->topClient->execute($req);
        var_dump($resp);


//        $req = new \ItemSellerGetRequest;
//        $req->setFields("num_iid,title,nick,price,approve_status,sku");
//        $req->setNumIid($num_iids); //564609484912
//        $resp = $this->topClient->execute($req, $sessionKey);
//
//        var_dump($resp);
    }

    private function createPwd($url, $text, $logo){
        $req = new \TbkTpwdCreateRequest();
        $req->setText($text);
        $req->setUrl($url);
        $req->setLogo($logo);
        $resp = $this->topClient->execute($req);
        if(!$resp || isset($resp->code)){
            return 0;
        }else{
            return $resp->data->model;
        }
    }

    private function queryPwd($content){
        $req = new \WirelessShareTpwdQueryRequest();
        $req->setPasswordContent($content);
        $resp = $this->topClient->execute($req);
        if(!$resp || isset($resp->code)){
            return 0;
        }else{
            return $resp->url;
        }
    }
}
