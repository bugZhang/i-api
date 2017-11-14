<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxContactController extends Controller
{
    //
    public function getMsg(Request $request){

        if(self::checkSignature($request)){
            echo $request->get('echostr');
        }else{
            echo 'error';
        }
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
