<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function test(){

        $c = new \TopClient();
        $c->appkey = env('TBK_APPID');
        $c->secretKey = env('TBK_SECRET');
        $c->format='json';
//        $c->platform = 2;


//        $req = new \TbkItemGetRequest();
//        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
//        $req->setQ("女装");
//        $req->setPageSize("20");
//        $resp = $c->execute($req);
//        var_export($resp);




//        $req = new \WirelessShareTpwdCreateRequest();
//        $tpwd_param = new \GenPwdIsvParamDto();
//        $tpwd_param->logo="http://img3.tbcdn.cn/tfscom/i1/3378282306/TB1iRZlc8fH8KJjy1XbXXbLdXXa_!!0-item_pic.jpg";
//        $tpwd_param->url="http://item.taobao.com/item.htm?id=559275604024";
//        $tpwd_param->text="超值活动，惊喜活动多多";
//        $req->setTpwdParam(json_encode($tpwd_param));
//        $resp = $c->execute($req);
//        var_export($resp);die();


//        $req = new \TbkTpwdCreateRequest;
////        $req->setUserId("123");
//        $req->setText("毛呢外套女秋冬2017新款中长款韩版厚羊毛茧型焦糖色呢子大衣女装");
//        $req->setUrl("http://item.taobao.com/item.htm?id=559275604024");
//        $req->setLogo("http://img3.tbcdn.cn/tfscom/i1/3378282306/TB1iRZlc8fH8KJjy1XbXXbLdXXa_!!0-item_pic.jpg");
////        $req->setExt("{}");
//        $resp = $c->execute($req);
//        var_export($resp);



//        $req = new \WirelessShareTpwdQueryRequest();
//        $req->setPasswordContent("我剁手都要买的宝贝（绿林手工热熔胶枪大号小号塑料玻璃热熔枪送热溶胶棒包邮20W-100W），快来和我一起瓜分红I包】http://a.nfi0.com/h.x8TyYx 点击链接，再选择浏览器打开；或复制这条信息￥0QEm0i3Qzmo￥后打开👉手淘👈");
//        $resp = $c->execute($req);
//        var_export($resp);


//        $req = new \TbkUatmFavoritesGetRequest();
//        $req->setPageNo("1");
//        $req->setPageSize("20");
//        $req->setFields("favorites_title,favorites_id,type");
//        $resp = $c->execute($req);
//        var_export($resp);


        $req = new \TbkUatmFavoritesItemGetRequest();
        $req->setPlatform("2");
        $req->setPageSize("20");
        $req->setAdzoneId("128300260");
        $req->setUnid("wechat");
        $req->setFavoritesId("14618601");
        $req->setPageNo("1");
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");
        $resp = $c->execute($req);
        var_export($resp);

    }
}
