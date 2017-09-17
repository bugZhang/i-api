<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AreaModel extends Model
{
    //

    const LEVEL_PROVINCE = 1;
    const LEVEL_CITY = 2;

    public function saveArea($areas){
        if($areas) {
            return DB::table('api_area')->insert($areas);
        }else{
            return 0;
        }
    }

    public function selectAreasByLevel($level, $parentId = 0){

        return DB::table('api_area')->where([
            ['level', '=', $level],
            ['parent_id', '=', $parentId]
        ])->get();

    }
}

