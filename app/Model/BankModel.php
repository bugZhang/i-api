<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BankModel extends Model
{
    protected $table = 'bank_branch_online';
    public $timestamps = false;


    public function selectNewBanksByAreaAndKeyword($bankCode, $keyword, $province, $page = 1){
        if(!$page || $page < 1){
            $page = 1;
        }
        $limit = 6;
        $offset = $limit * ($page - 1);
        $condition = [];

        if(is_numeric($keyword)){
            $condition[] = ['branch_bank_code', '=', $keyword];
            $banks = DB::table('banks')->where($condition)
                ->select('id', 'bank_name', 'bank_code', 'branch_bank_code', 'branch_bank_name')
                ->offset($offset)
                ->limit($limit)
                ->get();

        }else{
            $condition[] = ['bank_code', '=', $bankCode];
            $condition[] = ['province_name', '=', $province];

            $banks = DB::table('banks')->where($condition)
                ->whereRaw("MATCH(`branch_bank_name`, `branch_bank_address`) AGAINST (?)", [$keyword])
                ->select('id', 'bank_name', 'bank_code', 'branch_bank_code', 'branch_bank_name')
                ->offset($offset)
                ->limit($limit)
                ->get();

//            $banks = DB::table('banks')->where($condition)
//                ->where(function($query) use ($keyword){
//                    $query->where('branch_bank_name', 'like', '%' . $keyword . '%')
//                        ->orWhere(function($query) use ($keyword){
//                            $query->where('city_name', 'like', '%' . $keyword . '%');
//                        });
//                })
//                ->select('id', 'bank_name', 'bank_code', 'branch_bank_code', 'branch_bank_name')
//                ->offset($offset)
//                ->limit($limit)
//                ->get();
        }
        return $banks && $banks->count() ? $banks : false;
    }

    public function selectBanksByNameAndArea($bankCode, $keyword, $province, $page = 1){

        if(!$page || $page < 1){
            $page = 1;
        }
        $limit = 6;
        $offset = $limit * ($page - 1);
        $condition[] = ['bankCode', '=', $bankCode];
        $condition[] = ['provinceName', '=', $province];

        $this->saveKeyword($keyword);

        if(is_numeric($keyword)){
            $condition = [];
            $condition[] = ['code', '=', $keyword];
            $banks = $this->where($condition)
                ->select('id', 'code', 'name', 'address')
                ->offset($offset)
                ->limit($limit)
                ->get();
        }else{
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
        }

        return $banks && $banks->count() ? $banks : false;
    }

    public function saveKeyword($keyword){
        if($keyword){
            $this->from('wx_search_keyword')->insert(['content' => $keyword]);
        }
    }

}
