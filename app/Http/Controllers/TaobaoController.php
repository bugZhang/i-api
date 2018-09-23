<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class TaobaoController extends Controller
{


    private $topClient;
    private $ad_zoneId = '128300260';
    private $pageSize = 5;

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
//        $url    = 'http:' .$url;
//        $logo = 'https://img.alicdn.com/tfscom/i3/759179292/TB2hfLbfpXXXXcKXXXXXXXXXXXX_!!759179292.jpg';
//        $title = '酷毙灯LED台灯学生寝室宿舍led护眼台灯学习台灯床头灯可充电台灯';
//        $url = 'https://uland.taobao.com/coupon/edetail?e=NGSMQBiDUiUGQASttHIRqV4gYPzQfU52eE%2FZWGw7M20OH4xMRYRCNmH1qvx5ArxuoxtOCp1lYzWjC%2FhQv7OdIZQ5wfGz%2Fu%2BNGLN3c5IKM9MFjaZhgpTjjRlqjQc7%2B9fT';
//        $url = 'https://s.click.taobao.com/t?e=m%3D2%26s%3DTEuHo3ctN3tw4vFB6t2Z2ueEDrYVVa64XoO8tOebS%2Bfjf2vlNIV67sf07l3yvK80F%2FSaKyaJTUaUnQhjQHByW0y6jZVi51WCl4vbPH1BLgQ%2FPAyb7Sjf%2FotgwMO1YqTJ4q%2BRXokHRqt5Mf2rIwS0mYRPvA9GPmsyATWpNZv3pbpJIYSxSYJPubCf2sNOKhxVyGAj0bi%2Fw%2FeiC8fkRd4hDrXI5pLCNRb9cSpj5qSCmbA%3D&unid=wechat';
//        $url = 'http://h5.m.taobao.com/awp/core/detail.htm?id=549529358077';

        if(strpos($url, '//') === 0){
            $url = 'https:' . $url;
        }
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
        $items = '';
        if($items){
            $items = json_decode(unserialize($items));
            return $this->return_json('success', $items);
        }else{
            $req = new \TbkUatmFavoritesItemGetRequest();
            $req->setPlatform("2");
            $req->setPageSize($this->pageSize);
            $req->setAdzoneId($this->ad_zoneId);
            $req->setUnid("wechat");
            $req->setFavoritesId($favouriteId);
            $req->setPageNo($page);
            $req->setFields("num_iid,title,pict_url,reserve_price,zk_final_price,user_type,item_url,volume,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,coupon_click_url,click_url,type,coupon_info,coupon_remain_count");
            $resp = $this->topClient->execute($req);
            if(!$resp && isset($resp->code)){
                return $this->return_json('error', '未查询到数据');
            }else{
                $goodsList = $this->mergeGoodsList($resp->results->uatm_tbk_item);
                Redis::set($favourite_items_key, serialize(json_encode($goodsList, true)));
                Redis::expire($favourite_items_key, 60 * 30);
                return $this->return_json('success', $goodsList);
            }
        }
    }

    public function getCouponItems(Request $request){
        $keyword = $request->input('keyword');
        $page = $request->input('page') ?: 1;

        $req = new \TbkDgItemCouponGetRequest();
        $req->setAdzoneId($this->ad_zoneId);
        $req->setPlatform("2");
        $req->setPageSize($this->pageSize);
        $req->setQ($keyword);
        $req->setPageNo($page);
        $resp = $this->topClient->execute($req);
        if($resp && isset($resp->results)){
            $goodsList = $this->mergeGoodsList($resp->results->tbk_coupon);
            return $this->return_json('success', $goodsList);
        }else{
            return $this->return_json('error', $resp->error_response->sub_msg);
        }
    }

    private function getFavouriteList(){
        $req = new \TbkUatmFavoritesGetRequest();
        $req->setPageNo("1");
        $req->setPageSize($this->pageSize);
        $req->setFields("favorites_title,favorites_id,type");
//        $req->setType("1");
        $resp = $this->topClient->execute($req);
        var_export($resp);
    }

    public function getItemInfo($id){
        $this->getItemInfoByIds([$id]);
    }

    public function queryPwdFromPwd(Request $request){
        $pwd = $request->input('keyword');
//        $pwd = "【【4支装】云南白药牙膏留兰 薄荷 激爽 冰柠 帮助减轻牙龈问题】http://m.tb.cn/h.3Uz2PV9 点击链接，再选择浏览器咑閞；或復·制这段描述€15eVb3HJvs6€后到👉淘♂寳♀👈";

        $result = $this->queryPwd($pwd);
        if($result){
            $content = $result->content;
            if($content){
                return $this->searchMaterialByKeyword($content);
            }
        }
        return $this->return_json('error', '未查到优惠券');

    }

    private function searchMaterialByKeyword($keyword, $page = 1){
        $req = new \TbkDgMaterialOptionalRequest;
        $req->setPageNo($page);
        $req->setPageSize($this->pageSize);
        $req->setPlatform("2");
//        $req->setItemloc("杭州");
        $req->setQ($keyword);
        $req->setHasCoupon("true");
//        $req->setIp("13.2.33.4");
        $req->setAdzoneId($this->ad_zoneId);
        $resp = $this->topClient->execute($req);
        if($resp && $resp->total_results > 0){
            $goodsList = $resp->result_list->map_data;

            $num_iids = [];
            foreach ($goodsList as $item){
                $num_iids[] = $item->num_iid;
            }
            $infos = $this->getItemInfoByIds($num_iids);
            foreach ($goodsList as $item){
                if(!isset($item->click_url) && isset($item->url)){
                    $item->click_url = $item->url;
                }
                if(isset($item->coupon_share_url)){
                    $item->click_url = $item->coupon_share_url;
                    $item->coupon_click_url = $item->coupon_share_url;
                }

                if(isset($item->zk_final_price)){
                    $item->zk_final_coupon_price_wap = $item->zk_final_price;
                }
                if(isset($infos[$item->num_iid])){
                    $item->coupon_status = 1;
                    $item->goods_info = $infos[$item->num_iid];
                }else{
                    $item->goods_info = new \stdClass();
                    $item->goods_info->cat_leaf_name = '优惠已过期';
                    $item->coupon_status = 0;
                }
            }
            return $this->return_json('success', $goodsList);
        }else{
            return $this->return_json('error', '未查询到信息');
        }
    }

    public function searchMaterial(Request $request){
        $keyword    = $request->input('keyword');
        $page       = $request->input('page');

        return $this->searchMaterialByKeyword($keyword, $page);
    }

    public function searchBykeyword($keyword, $page = 1){
        if(!$keyword){
            return $this->return_json('error', '请输入要查询的关键词');
        }

        $req = new \TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ($keyword);
        $req->setSort("tk_rate_des");
        $req->setPlatform("2");         //链接形式：1：PC，2：无线，默认：１
        $req->setStartTkRate("123");
//        $req->setEndTkRate("1000");
        $req->setPageNo($page);
        $req->setPageSize($this->pageSize);
        $resp = $this->topClient->execute($req);
        if($resp && isset($resp->results->n_tbk_item)){
            $goodsList = $resp->results->n_tbk_item;
            $num_iids = [];
            foreach ($goodsList as $item){
                $num_iids[] = $item->num_iid;
            }
            $infos = $this->getItemInfoByIds($num_iids);
            foreach ($goodsList as $item){
                if(!isset($item->click_url) && isset($item->item_url)){
                    $item->click_url = $item->item_url;
                }
                if(isset($item->zk_final_price)){
                    $item->zk_final_coupon_price_wap = $item->zk_final_price;
                }
                if(isset($infos[$item->num_iid])){
                    $item->coupon_status = 1;
                    $item->goods_info = $infos[$item->num_iid];
                }else{
                    $item->goods_info = new \stdClass();
                    $item->goods_info->cat_leaf_name = '优惠已过期';
                    $item->coupon_status = 0;
                }
            }

            return $this->return_json('success', $goodsList);
        }else{
            return $this->return_json('error', $resp->error_response->sub_msg);
        }
    }

    public function searchGoodsByKeyword(Request $request){
        $keyword = $request->input('keyword');
        $page = $request->input('page');

        return $this->searchBykeyword($keyword, $page);
    }

    private function getItemInfoByIds( array $num_iids, $platform = '2'){
        if(!$num_iids){
            return 0;
        }
        $tbk_item_key = 'tbk_item:';
        $req = new \TbkItemInfoGetRequest();
        $req->setPlatform($platform);
        $num_iids = implode(',', $num_iids);
        $req->setNumIids($num_iids);
        $resp = $this->topClient->execute($req);
        if($resp && $resp->results->n_tbk_item){
            $items = [];
            foreach ($resp->results->n_tbk_item as $item){
                $items[$item->num_iid] = $item;
//                Redis::hMSet($tbk_item_key . $item->num_iid, $item);
            }
            return $items;
        }else{
            return 0;
        }
    }

    private function createPwd($url, $text, $logo){

        if(!$url){
            return 0;
        }elseif(stripos($url, 'uland.taobao.com') !== false || stripos($url, 'click.taobao.com') !== false){
            $req = new \TbkTpwdCreateRequest();
            $req->setText($text);
            $req->setUrl($url);
            $req->setLogo($logo);
        }else{
            $req = new \WirelessShareTpwdCreateRequest;
            $tpwd_param = new \GenPwdIsvParamDto;
            $tpwd_param->logo = $logo;
            $tpwd_param->url = $url;
            $tpwd_param->text = $text;
            $req->setTpwdParam(json_encode($tpwd_param));
        }
        $resp = $this->topClient->execute($req);
        if(!$resp || isset($resp->code)){
            return 0;
        }else{
            return isset($resp->model) ? $resp->model : $resp->data->model;
        }
    }


    private function mergeGoodsList($goodsList){
        if($goodsList){
            $num_iids = [];
            foreach ($goodsList as $item){
                $num_iids[] = $item->num_iid;
                $item->zk_final_price_wap = isset($item->zk_final_price_wap) ? $item->zk_final_price_wap : $item->zk_final_price;
                if(isset($item->coupon_info)){
                    $pattern = '/(?P<coupon_limit_money>\d+)(.*\D)(?P<coupon_money>\d+)/';
                    preg_match($pattern, $item->coupon_info, $matches);
                    if(isset($matches['coupon_money']) &&
                        isset($matches['coupon_limit_money']) &&
                        $item->zk_final_price_wap >= $matches['coupon_limit_money']
                    ){
                        $item->coupon_money = $matches['coupon_money'];
                        $item->zk_final_coupon_price_wap = $item->zk_final_price_wap - $matches['coupon_money'];
                        $item->zk_final_coupon_price_wap = floatval(number_format($item->zk_final_coupon_price_wap, 2));
                    }
                }else{
                    $item->zk_final_coupon_price_wap = $item->zk_final_price_wap;
                }
            }

            $infos = $this->getItemInfoByIds($num_iids);
            foreach ($goodsList as $item){
                if(!isset($item->click_url) && isset($item->item_url)){
                    $item->click_url = $item->item_url;
                }
                if(isset($infos[$item->num_iid])){
                    $item->coupon_status = 1;
                    $item->goods_info = $infos[$item->num_iid];
                }else{
                    $item->goods_info = new \stdClass();
                    $item->goods_info->cat_leaf_name = '优惠已过期';
                    $item->coupon_status = 0;
                }
            }
            return $goodsList;
        }else{
            return 0;
        }
    }


    private function queryPwd($content){
        $req = new \WirelessShareTpwdQueryRequest();
        $req->setPasswordContent($content);
        $resp = $this->topClient->execute($req);
        if(!$resp || isset($resp->code)){
            return 0;
        }else{
            return $resp;
        }
    }
}
