<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaobaoController extends Controller
{


    private $topClient;
    private $ad_zoneId = '128300260';

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

        $logo = 'http://img4.tbcdn.cn/tfscom/i1/3074135292/TB24qNhdL2H8KJjy1zkXXXr7pXa_!!3074135292.jpg';
        $title = '心逸原木30包抽纸整箱婴儿面巾纸纸巾家庭装餐巾纸卫生纸纸抽批发';
        $url = 'http://h5.m.taobao.com/awp/core/detail.htm?id=553070734386';

        $pwd = $this->createPwd($url, $title, $logo);
        if($pwd){
            return $this->return_json('success', ['pwd' => $pwd]);
        }else{
            return $this->return_json('error', '生成口令码失败！');
        }

    }

    public function getFavouriteItems($favouriteId, $page){
        $req = new \TbkUatmFavoritesItemGetRequest();
        $req->setPlatform("2");
        $req->setPageSize("20");
        $req->setAdzoneId($this->ad_zoneId);
        $req->setUnid("wechat");
        $req->setFavoritesId($favouriteId);
        $req->setPageNo($page);
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");
        $resp = $this->topClient->execute($req);
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

    private function getItemInfo($num_iids, $platform = 2){
        $req = new \TbkItemInfoGetRequest();
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url");
        $req->setPlatform($platform);
        $req->setNumIids($num_iids);
        $resp = $this->topClient->execute($req);
        var_dump($resp);
    }

    private function createPwd($url, $text, $logo){
        $req = new \WirelessShareTpwdCreateRequest();
        $tpwd_param = new \GenPwdIsvParamDto();
        $tpwd_param->logo = $logo;
        $tpwd_param->url = $url;
        $tpwd_param->text = $text;
        $req->setTpwdParam(json_encode($tpwd_param));
        $resp = $this->topClient->execute($req);
        if(!$resp || isset($resp->code)){
            return 0;
        }else{
            return $resp->model;
        }
    }

    private function queryPwd(){

    }
}
