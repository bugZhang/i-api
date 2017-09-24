<?php

namespace App\Traits;

trait ResponseJson{

    public function return_json($status){
        $args		= func_get_args();
        $argsNum 	= count($args);
        $data['status']	= $status;

        if ($argsNum == 2){
            if(is_string($args[1])){
                $data['status']	= $status;
                $data['info'] 	= $args[1];
            }
            if(is_object($args[1])){
                $args[1] = (array) $args[1];
            }
            if(is_array($args[1])){
//                $data = array_merge($data, $args[1]);
                $data['data'] = $args[1];
            }
        }elseif ($argsNum == 3){
            if(is_string($args[0]) && is_string($args[1]) && is_string($args[2])){
                $data['status'] = $args[0];
                $data['info'] = $args[1];
                $data['field'] = $args[2];
            }elseif (is_string($args[0]) && is_array($args[1]) && is_string($args[2])){
                $data['status'] = $args[0];
                $data['data'] = $args[1];
                $data['info'] = $args[2];
            }
        }
        return response()->json($data);
    }

}