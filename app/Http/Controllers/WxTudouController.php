<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::error('!1111111111111111');
        $msg = $request->getContent();
        if($msg){
            Log::error('-=-=-=-=-=-=-=-=');
            $xmlObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = $xmlObj->MsgType;

            $content = $xmlObj->Content;

            Log::error($msgType);
            Log::error($content);
        }else{
            Log::error('+++++++++++++++');
        }
        return 'success';

    }

}
