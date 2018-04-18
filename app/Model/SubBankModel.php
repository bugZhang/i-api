<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SubBankModel extends Model{

    public $timestamps = false;

    public function addArea($params){

        $this->from('area_pool')->insert($params);
    }

}
