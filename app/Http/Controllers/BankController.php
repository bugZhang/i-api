<?php

namespace App\Http\Controllers;

use App\Model\BankModel;
use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    private $newBanks = [
        103 => '中国农业银行',
        102 => '中国工商银行',
        104 => '中国银行',
        105 => '中国建设银行',
        403 => '中国邮政储蓄银行',
        301 => '交通银行',
        308 => '招商银行',
        302 => '中信银行',
        305 => '中国民生银行',
        309 => '兴业银行',
        303 => '中国光大银行',
        310 => '上海浦东发展银行',
        304 => '华夏银行',
        306 => '广发银行',
        307 => '平安银行',
        316 => '浙商银行',
        317 => '农村合作银行',
    ];

    public function getNewBanks(Request $request, $bankCode, $province, $keyword, $page = 1){
        $openid = $request->wx_openid;

        if(!isset($this->newBanks[$bankCode])){
            return $this->return_json('error', '参数错误！');
        }

        $bankModel  = new BankModel();
        $banks = $bankModel->selectNewBanksByAreaAndKeyword($bankCode, $keyword, $province, $page);

        if($banks){
            $returnData = [];
            if($openid){
                $wxCollectModel = new WxBankCollectModel();
                $collectCodes = $wxCollectModel->selectCollectBankCodeByOpenid($openid);
                if($collectCodes){
                    foreach ($collectCodes as $coll){
                        $returnData['collects'][] = $coll->bank_code;
                    }
                }
            }
            foreach ($banks as $bank){
                $temp = [];
                $temp['bank_name'] = $bank->bank_name;
                $temp['bank_code'] = $bank->bank_code;
                $temp['branch_bank_code'] = $bank->branch_bank_code;
                $temp['branch_bank_short_name'] = preg_replace("/^" . $this->newBanks[$bankCode] . "/", '', $bank->branch_bank_name, 1);
                $temp['branch_bank_short_name'] = preg_replace("/^股份有限公司/", '', $temp['branch_bank_short_name'], 1);

                $temp['branch_bank_name'] = $bank->branch_bank_name;

                $temp['id'] = $bank->id;
                if(isset($returnData['collects']) && in_array($bank->branch_bank_code, $returnData['collects'])){
                    $temp['is_collect'] = 1;
                }else{
                    $temp['is_collect'] = 0;
                }

                $data[] = $temp;
            }
            $returnData['banks'] = $data;
            $returnData['count'] = $banks->count();

            return $this->return_json('success', $returnData);

        }else{
            return $this->return_json('error', '未查询到结果');
        }
    }

    public function getBanks(Request $request, $bankCode, $province, $keyword, $page = 1){

        $openid = $request->wx_openid;

        $bankModel  = new BankModel();
        $banks = $bankModel->selectBanksByNameAndArea($bankCode, $keyword, $province, $page);
        if($banks){
            $returnData = [];
            if($openid){
                $wxCollectModel = new WxBankCollectModel();
                $collectCodes = $wxCollectModel->selectCollectBankCodeByOpenid($openid);
                if($collectCodes){
                    foreach ($collectCodes as $coll){
                        $returnData['collects'][] = $coll->bank_code;
                    }
                }
            }
            foreach ($banks as $bank){
                $temp = [];
                $temp['name'] = $bank->name;
                $temp['address'] = $bank->address;
                $temp['code'] = $bank->code;
                $temp['id'] = $bank->id;
                if(isset($returnData['collects']) && in_array($bank->code, $returnData['collects'])){
                    $temp['is_collect'] = 1;
                }else{
                    $temp['is_collect'] = 0;
                }

                $data[] = $temp;
            }
            $returnData['banks'] = $data;
            $returnData['count'] = $banks->count();

            return $this->return_json('success', $returnData);

        }else{
            return $this->return_json('error', '未查询到结果');
        }

    }
}
