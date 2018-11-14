<?php

namespace App\Http\Controllers;

use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class WxBankCollectController extends Controller
{

    private $collectMaxCount = 5;

    public function getUserCollect(Request $request){
        $sessionId   = $request->header('p-sid');
        if(!$sessionId){
            Log::error('请求头信息中没有p-sid' . $sessionId);
            return $this->return_json('error', '参数错误');
        }
        $hKey = env('WX_REDIS_SESSION_PREFIX') . $sessionId;
        $openid = Redis::hget($hKey, 'openid');
        if(!$openid){
            Log::error('缓存信息中未找到openid，登陆过期，获取收藏失败');
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

        if(isset($request->isNewBank)){
            $this->collectMaxCount = 6;
        }

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


    public function getNewBankUserCollect(Request $request){
        $sessionId   = $request->header('p-sid');
        if(!$sessionId){
            Log::error('请求头信息中没有p-sid' . $sessionId);
            return $this->return_json('error', '参数错误');
        }
        $hKey = env('WX_NEW_BANK_REDIS_SESSION_PREFIX') . $sessionId;
        $openid = Redis::hget($hKey, 'openid');
        if(!$openid){
            Log::error('缓存信息中未找到openid，登陆过期，获取收藏失败');
            return $this->return_json('error', '登陆过期');
        }
        $wxCollectModel = new WxBankCollectModel();
        $banks = $wxCollectModel->selectNewBankCollectByOpenid($openid);
        if($banks){
            $banks = $this->mergeNewBanks($banks);
            return $this->return_json('success', $banks->toArray());
        }else{
            return $this->return_json('empty', '未找到数据');
        }
    }

    private function mergeNewBanks($collectBanks){
        if(empty($collectBanks)) return false;
        foreach ($collectBanks as $bank) {
            $bank->branch_bank_short_name = preg_replace("/^" . $bank->bank_name . "/", '', $bank->branch_bank_name, 1);
            $bank->branch_bank_short_name = preg_replace("/^股份有限公司/", '', $bank->branch_bank_short_name, 1);
        }
        return $collectBanks;
    }

}
