<?php

namespace App\Http\Controllers;

use App\Model\TbkTrackModel;
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

    private function setTopClient(){
        if(!$this->topClient){
            $this->topClient = new \TopClient(env('TBK_APPID'), env('TBK_SECRET'));
            $this->topClient->format = 'json';
        }
    }

    public function test(){
        $req = new \TbkUatmFavoritesGetRequest();
        $req->setPageNo("1");
        $req->setPageSize($this->pageSize);
        $req->setFields("favorites_title,favorites_id,type");
        $resp = $this->topClient->execute($req);
        var_export($resp);
    }

    /**
     * 生成淘口令
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sharePwd(Request $request){
        $logo = $request->input('logo');
        $title  = $request->input('title');
        $url    = $request->input('url');

        if(strpos($url, '//') === 0){
            $url = 'https:' . $url;
        }
        $pwd = $this->createPwd($url, $title, $logo);
        if($pwd){
            $tbkTrackModel   = new TbkTrackModel();
            $trackInfo       = [];
            $trackInfo['title'] = $title;
            $trackInfo['url']   = $url;
            $trackInfo['action']    = TbkTrackModel::ACTION_2;
            $trackInfo['track_type']    = $tbkTrackModel::TYPE_CLICK;
            $tbkTrackModel->addTrackInfo($trackInfo);

            return $this->return_json('success', ['pwd' => $pwd]);
        }else{
            return $this->return_json('error', '生成口令码失败！');
        }

    }

    /**
     * 根据选品库id，查询选品库中的产品
     * @param $favouriteId
     * @param $page
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFavouriteItems($favouriteId, $page){
        $favourite_items_key = 'tbk_favourite_items:' . $favouriteId . ':' . $page;

        $items = Redis::get($favourite_items_key);
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

    /**
     * 通过淘口令查询产品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryPwdFromPwd(Request $request){
        $pwd = $request->input('keyword');
        $result = $this->queryPwd($pwd);
        $tbkTrackModel   = new TbkTrackModel();
        $trackInfo       = [];
        $trackInfo['keyword'] = $pwd;
        $trackInfo['action']    = TbkTrackModel::ACTION_5;
        $trackInfo['track_type']    = $tbkTrackModel::TYPE_CLICK;
        $tbkTrackModel->addTrackInfo($trackInfo);
        if($result){
            $content = $result->content;
            if($content){
                return $this->searchMaterialByKeyword($content);
            }
        }
        return $this->return_json('error', '未查到优惠券');

    }

    public function searchMaterial(Request $request){
        $keyword    = $request->input('keyword');
        $page       = $request->input('page');

        $tbkTrackModel   = new TbkTrackModel();
        $trackInfo       = [];
        $trackInfo['keyword'] = $keyword;
        $trackInfo['action']    = TbkTrackModel::ACTION_1;
        $trackInfo['track_type']    = $tbkTrackModel::TYPE_CLICK;
        $tbkTrackModel->addTrackInfo($trackInfo);

        return $this->searchMaterialByKeyword($keyword, $page);
    }

    /**
     * 根据关键字查询产品
     * @param $keyword
     * @param int $page
     * @return \Illuminate\Http\JsonResponse
     */
    private function searchMaterialByKeyword($keyword, $page = 1){
        $req = new \TbkDgMaterialOptionalRequest;
        $req->setPageNo($page);
        $req->setPageSize($this->pageSize);
        $req->setPlatform("2");
        $req->setQ($keyword);
        $req->setHasCoupon("true");
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

    /**
     * 创建淘口令
     * @param $url
     * @param $text
     * @param $logo
     * @return int
     */
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

    public function getItemInfo($id){
        $this->getItemInfoByIds([$id]);
    }
}
