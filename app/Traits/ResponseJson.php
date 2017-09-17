<?php

namespace App\Traits;

trait ResponseJson{

    public function return_json($status, $data){
        if($status == null || $status == ''){
            $status = 'error';
        }
        if(!is_array($data) && is_string($data)){
            $data = ['msg'=>$data];
        }

        $res = ['status' => $status, 'data' => $data];
        return response()->json($res);
    }

}