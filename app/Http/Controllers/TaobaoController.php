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
        var_export($resp);
    }

    public function sharePwd(){

    }

}
