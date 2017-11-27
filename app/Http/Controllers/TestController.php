<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test(){

        $c = new \TopClient();
        $c->appkey = '';
        $c->secretKey = '';
        $c->format='json';
//        $c->platform = 2;


//        $req = new \TbkItemGetRequest();
//        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
//        $req->setQ("女装");
//        $req->setPageSize("20");
//        $resp = $c->execute($req);
//        var_export($resp);




        $req = new \WirelessShareTpwdCreateRequest();
        $tpwd_param = new \GenPwdIsvParamDto();
//        $tpwd_param->ext="{\"xx\":\"xx\"}";
        $tpwd_param->logo="http://img3.tbcdn.cn/tfscom/i1/3378282306/TB1iRZlc8fH8KJjy1XbXXbLdXXa_!!0-item_pic.jpg";
        $tpwd_param->url="http://item.taobao.com/item.htm?id=559275604024";
        $tpwd_param->text="超值活动，惊喜活动多多";
//        $tpwd_param->user_id="24234234234";
        $req->setTpwdParam(json_encode($tpwd_param));
        $resp = $c->execute($req);
        var_export($resp);die();


//        $req = new \TbkTpwdCreateRequest;
////        $req->setUserId("123");
//        $req->setText("毛呢外套女秋冬2017新款中长款韩版厚羊毛茧型焦糖色呢子大衣女装");
//        $req->setUrl("http://item.taobao.com/item.htm?id=559275604024");
//        $req->setLogo("http://img3.tbcdn.cn/tfscom/i1/3378282306/TB1iRZlc8fH8KJjy1XbXXbLdXXa_!!0-item_pic.jpg");
////        $req->setExt("{}");
//        $resp = $c->execute($req);
//        var_export($resp);

    }
}
