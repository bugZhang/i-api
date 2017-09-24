<?php

namespace App\Http\Controllers;

use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WxBankCollectController extends Controller
{

    private $collectMaxCount = 5;

    public function getUserCollect(Request $request){
        $sessionId   = $request->header('p-sid');
        if(!$sessionId){
            return $this->return_json('error', '参数错误');
        }
        $hKey = env('WX_REDIS_SESSION_PREFIX') . $sessionId;
        $openid = Redis::hget($hKey, 'openid');
        if(!$openid){
            return $this->return_json('error', '登陆过期');
        }
        $wxCollectModel = new WxBankCollectModel();
        $banks = $wxCollectModel->selectCollectByOpenid($openid);
        if($banks){
            return $this->return_json('success', $banks->toArray());
        }else{
            return $this->return_json('empty', '未找到数据');
        }
    }

    public function deleteUserCollect(Request $request, $bankCode){
        $openid = $request->wx_openid;
        if(!$openid || !$bankCode){
            return $this->return_json('error', '获取参数失败');
        }
        $wxCollectModel = new WxBankCollectModel();
        $collect = $wxCollectModel->selectCollectByOpenidAndBankCode($openid, $bankCode);
        if($collect){
            $result = $wxCollectModel->deleteCollect($openid, $bankCode);
            if($result){
                return $this->return_json('success', '取消成功');
            }else{
                return $this->return_json('error', '取消失败');
            }
        }else{
            return $this->return_json('notExist', '未存在记录');
        }
    }

    public function saveUserCollect(Request $request, $bankCode){

        $openid = $request->wx_openid;
        if(!$openid || !$bankCode){
            return $this->return_json('error', '获取参数失败');
        }
        $wxCollectModel = new WxBankCollectModel();
        $collectCount = $wxCollectModel->selectCollectCountByOpenid($openid);
        if($collectCount >= $this->collectMaxCount){
            return $this->return_json('over', '最多只能收藏' . $this->collectMaxCount . '条记录');
        }
        $collect = $wxCollectModel->selectCollectByOpenidAndBankCode($openid, $bankCode);
        if($collect){
            return $this->return_json('repeat', '本条记录已存在');
        }else{
            $result = $wxCollectModel->addCollect($openid, $bankCode);
            if($result){
                return $this->return_json('success', '收藏成功');
            }else{
                return $this->return_json('error', '收藏失败');
            }
        }
    }

}
