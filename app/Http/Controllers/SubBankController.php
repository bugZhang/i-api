<?php

namespace App\Http\Controllers;

use App\Model\SubBankModel;
use Illuminate\Http\Request;

class SubBankController extends Controller
{
    public function saveArea(Request $request){

        $params['bank_code'] = $request->query('bank_code');
        $params['bank_name'] = $request->query('bank_name');
        $params['province_code'] = $request->query('province_code');
        $params['province_name'] = $request->query('province_name');
        $params['city_code'] = $request->query('city_code');
        $params['city_name'] = $request->query('city_name');
        if(empty($params['bank_code'])){
            return $this->return_json('error');
        }

        $bankModel  = new SubBankModel();
        $bankModel->addArea($params);

        return $this->return_json('success', $params);

    }

}
