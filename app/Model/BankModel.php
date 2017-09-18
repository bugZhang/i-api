<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BankModel extends Model
{
    public function selectBanksByNameAndArea($bankCode, $keyword, $province, $city){

        $condition[] = ['bankCode', '=', $bankCode];
        $condition[] = ['name', 'like', '%' . $keyword . '%'];
        $condition[] = ['provinceName', '=', $province];
        if($city){
            $condition[] = ['cityName', '=', $city];
        }
        $banks = DB::table('bank_branch_online')->where($condition)->select('id', 'code', 'name', 'address')->limit(6)->get();
        return $banks;
    }

}