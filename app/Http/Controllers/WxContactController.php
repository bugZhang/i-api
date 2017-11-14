<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WxContactController extends Controller
{
    //
    public function getMsg(Request $request){

        Log::error('开始接收消息');
        Log::error($request->get('signature'));

        Log::error('json', [$request->json('Content')]);

        if(!$this->checkSignature($request)){
            Log::error('接收消息 检查签名失败');
            echo '';die();
        }

        $toUserName = $request->json('ToUserName');
        $content = $request->json('Content');
        $fromUserName   = $request->json('FromUserName');
        $msgId  = $request->json('MsgId');
        Log::error($toUserName. '   ' . $fromUserName . '  ' . $content . '  ' . $msgId);
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
