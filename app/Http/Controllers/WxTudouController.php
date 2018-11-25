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
        Log::error('!1111111111111111');
        $msg = $request->getContent();
        if($msg){
            $msgObj = simplexml_load_string($msg, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = $msgObj->MsgType;
            $content = $msgObj->Content;
//            $msgObj->ToUserName;
//            $msgObj->FromUserName;
//            $msgObj->CreateTime;
//            $msgObj->Content = '测试成功';
//            $msgObj->MsgId;
//            $msgObj->MsgType = 'text';


            $responseMsg['MsgType'] = 'text';
            $responseMsg['Content'] = '测试成功';
            $responseMsg['ToUserName'] = $msgObj->ToUserName;
            $responseMsg['FromUserName'] = $msgObj->FromUserName;


            return view('weixin.responseMsg', $responseMsg);
//            return response()->view('weixin.responseMsg', $msgObj);
//            return View::make('weixin.responseMsg')->with('message', $message);

        }else{
            Log::error('+++++++++++++++');
        }
        return 'success';

    }

}
