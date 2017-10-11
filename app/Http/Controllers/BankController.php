<?php

namespace App\Http\Controllers;

use App\Model\BankModel;
use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{

    public function getBanks(Request $request, $bankCode, $province, $city, $keyword, $page = 1){

        if(in_array($province, ['天津市', '重庆市', '北京市', '上海市'])){
            $city = $province;
        }
        $openid = $request->wx_openid;

        $bankModel  = new BankModel();
        $banks = $bankModel->selectBanksByNameAndArea($bankCode, $keyword, $province, $city, $page);
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
