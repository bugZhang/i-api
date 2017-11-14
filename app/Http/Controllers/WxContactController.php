<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WxContactController extends Controller
{
    //
    public function getMsg(Request $request){

        Log::error('开始接收消息');

        Log::error($request->get('text'));
        Log::error($request->get('signature'));
        Log::error($request->get('timestamp'));
        Log::error($request->get('nonce'));
        Log::error($request->get('Content'));
        Log::error($request->all());

        if($this->checkSignature($request)){
            Log::error('检查签名通过');
        }else{
            Log::error('检查签名失败');
        }

        $toUserName = $request->json('ToUserName');

        $content = $request->json('Content');

        Log::info($toUserName. '   ' . $content);
        echo 'success';
    }




    private function checkSignature(Request $request)
    {

        $signature = $request->get('signature');
        $timestamp = $request->get('timestamp');
        $nonce      = $request->get('nonce');

        $token = env('WX_MSG_TOKEN');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

}
