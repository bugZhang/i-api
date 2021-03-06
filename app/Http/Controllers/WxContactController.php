<?php

namespace App\Http\Controllers;

use App\Model\WxMsgModel;
use App\Service\WxContactService;
use App\Service\WxLoginService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WxContactController extends Controller
{
    //
    public function getMsg(Request $request){

        Log::error('json', [$request->json('Content')]);
        if(!$this->checkSignature($request)){
            Log::error('接收消息 检查签名失败');
            echo '';die();
        }

        $toUserName = $request->json('ToUserName');
        $content = $request->json('Content');
        $fromUserName   = $request->json('FromUserName');
        $msgId  = $request->json('MsgId');
        $msgType = $request->json('MsgType');
        $picUrl = $request->json('PicUrl');

        if($content){
            $msg = [
                'id'            => $msgId,
                'to_user_name'  => $toUserName,
                'from_user_name'=> $fromUserName,
                'msg_type'      => $msgType,
                'content'       => $content ? $content : ($picUrl ? $picUrl : '')
            ];
            $wxMsgModel = new WxMsgModel();
            $wxMsgModel->saveMsg($msg);
            $response = $this->sendTextMsg($fromUserName, '已收到您的消息，谢谢您的反馈');
            Log::error('发送消息状态', [$response]);
        }
        echo 'success';
    }

    /**
     * 发送消息给用户
     * @param $openId
     * @param $content
     */
    private function sendTextMsg($openId, $content){

        if(!$openId || !$content){
            return false;
        }
        $msg = [
            'touser'    => $openId,
            'msgtype'   => 'text',
            'text'      => [
                'content'   => $content
            ]
        ];

        $loginService = new WxLoginService();
        $accessToken    = $loginService->getWxAccessToken();
        if(!$accessToken){
            return 0;
        }
        $msgService = new WxContactService();
        return $msgService->sendMsg($msg, $accessToken);
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
