<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaobaoController extends Controller
{


    private $topClient;

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


    }

    public function queryPwd(Request $request){

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

}
