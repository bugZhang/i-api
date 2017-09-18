<?php

namespace App\Http\Controllers;

use App\Model\BankModel;
use Illuminate\Http\Request;

class BankController extends Controller
{

    public function getBanks($bankCode, $province, $city, $keyword){

        $bankModel  = new BankModel();
        $banks = $bankModel->selectBanksByNameAndArea($bankCode, $keyword, $province, $city);

        if($banks){
            foreach ($banks as $bank){
                $temp = [];
                $temp['name'] = $bank->name;
                $temp['address'] = $bank->address;
                $temp['code'] = $bank->code;
                $temp['id'] = $bank->id;
                $data[] = $temp;
            }
            return $this->return_json('success', ['data'=>$data]);

        }else{
            return $this->return_json('error', '未查询到结果');
        }

    }
}
