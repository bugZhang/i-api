<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BankModel extends Model
{
    public function selectBanksByNameAndArea($bankCode, $keyword, $province, $city, $page = 1){

        if(!$page || $page < 1){
            $page = 1;
        }
        $limit = 6;
        $offset = $limit * ($page - 1);
        $condition[] = ['bankCode', '=', $bankCode];
        $condition[] = ['name', 'like', '%' . $keyword . '%'];
        $condition[] = ['provinceName', '=', $province];
        if($city){
            $condition[] = ['cityName', '=', $city];
        }
        $banks = DB::table('bank_branch_online')->where($condition)
            ->select('id', 'code', 'name', 'address')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return $banks && count($banks) > 0 ? $banks : false;
    }

}