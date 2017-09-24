<?php

namespace App\Http\Controllers;

use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;

class WxBankCollectController extends Controller
{
    //

    private $collectMaxCount = 5;

    public function getUserCollect($openid){
        if(!$openid){
            $this->return_json('error', '参数错误');
        }

        $wxCollectModel = new WxBankCollectModel();
        $banks = $wxCollectModel->selectCollectByOpenid($openid);
        var_dump($banks->count());
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
            $this->return_json('repeat', '本条记录已存在');
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
