<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BankModel extends Model
{
    protected $table = 'bank_branch_online';
    public $timestamps = false;

    public function selectBanksByNameAndArea($bankCode, $keyword, $province, $city, $page = 1){

        if(!$page || $page < 1){
            $page = 1;
        }
        $limit = 6;
        $offset = $limit * ($page - 1);
        $condition[] = ['bankCode', '=', $bankCode];
        $condition[] = ['provinceName', '=', $province];
//        if($city){
//            $condition[] = ['cityName', '=', $city];
//        }
        $banks = $this->where($condition)
            ->where(function($query) use ($keyword){
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere(function($query) use ($keyword){
                        $query->where('address', 'like', '%' . $keyword . '%');
                    });
            })
            ->select('id', 'code', 'name', 'address')
            ->offset($offset)
            ->limit($limit)
            ->get();
        return $banks && $banks->count() ? $banks : false;
    }

}