<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WxTudouController extends Controller
{

//    public function test1(Request $request) {
//
//        $signature = $request->input('signature');
//        $timestamp = $request->input('timestamp');
//        $nonce     = $request->input('nonce');
//        $echostr   = $request->input('echostr');
//
//        $token = config('app.WX_GZH_TOKEN');
//
//        $tmpArr = array($token, $timestamp, $nonce);
//        sort($tmpArr, SORT_STRING);
//        $tmpStr = implode( '', $tmpArr );
//        $tmpStr  = sha1($tmpStr);
//
//        if( $tmpStr == $signature ){
//            return $echostr;
//        }else{
//            return false;
//        }
//
//    }


    public function getMsg(Request $request){
        $msg = $request->getContent();
        if($msg){
            $msgObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);

            Log::error('留言信息：' .  $msgObj->Content);
            $responseMsg['MsgType'] = 'text';


            $responseMsg['Content'] = '测试成功';
            $responseMsg['ToUserName'] = $msgObj->ToUserName;
            $responseMsg['FromUserName'] = $msgObj->FromUserName;
            return view('weixin.responseMsg', $responseMsg);
        }
        return 'success';

    }

}
