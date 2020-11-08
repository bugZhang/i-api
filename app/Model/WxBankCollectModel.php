<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WxBankCollectModel extends Model
{
    protected $table = 'wx_bank_collect';
    public $timestamps = false;


    public function selectCollectByOpenid($openid){

        if(!$openid){
            return 0;
        }
        $result = $this->leftJoin('bank_branch_online', 'wx_bank_collect.bank_code', '=', 'bank_branch_online.code')
            ->where('wx_bank_collect.open_id', '=', $openid)
            ->select('open_id', 'bank_code', 'name')
            ->get();
        return $result->count() > 0 ? $result : 0;

    }


    public function selectNewBankCollectByOpenid($openid){

        if(!$openid){
            return 0;
        }
        $result = $this->leftJoin('banks', 'wx_bank_collect.bank_code', '=', 'banks.branch_bank_code')
            ->where('wx_bank_collect.open_id', '=', $openid)
            ->select('branch_bank_code', 'branch_bank_name', 'bank_name', 'banks.bank_code')
            ->get();
        return $result;

    }

    public function selectCollectBankCodeByOpenid($openid){
        if(!$openid){
            return 0;
        }
        $result = $this->where('open_id', '=', $openid)
            ->select('bank_code')
            ->get();
        return $result->count() > 0 ? $result : 0;
    }

    public function selectCollectByOpenidAndBankCode($openid, $bankCode){
        if(!$openid || !$bankCode){
            return 0;
        }
        $result = $this->where([
            ['open_id', '=', $openid],
            ['bank_code', '=', $bankCode]
        ])->get();

        return $result->count() > 0 ? $result : 0;
    }

    public function selectCollectCountByOpenid($openid){
        if(!$openid){
            return 0;
        }
        return $this->where('open_id', '=', $openid)->count('bank_code');
    }

    public function addCollect($openid, $bankCode){

        if(!$openid || !$bankCode){
            return 0;
        }
        return $this->insert([
            'open_id'   => $openid,
            'bank_code'   => $bankCode
        ]);
    }

    public function deleteCollect($openid, $bankCode){

        if(!$openid || !$bankCode){
            return 0;
        }
        return $this->where([
            ['open_id', '=', $openid],
            ['bank_code', '=', $bankCode]
        ])->delete();
    }

}
