<?php

namespace App\Http\Controllers;

use App\Model\BankModel;
use App\Model\WxBankCollectModel;
use Illuminate\Http\Request;

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
            foreach ($banks as $bank){
                $temp = [];
                $temp['name'] = $bank->name;
                $temp['address'] = $bank->address;
                $temp['code'] = $bank->code;
                $temp['id'] = $bank->id;
                $data[] = $temp;
            }
            $returnData['banks'] = $data;
            $returnData['count'] = $banks->count();
            if($openid){
                $wxCollectModel = new WxBankCollectModel();
                $collectCodes = $wxCollectModel->selectCollectBankCodeByOpenid($openid);
                if($collectCodes){
                    $returnData['collects'] = $collectCodes;
                }
            }
            return $this->return_json('success', $returnData);

        }else{
            return $this->return_json('error', '未查询到结果');
        }

    }
}
