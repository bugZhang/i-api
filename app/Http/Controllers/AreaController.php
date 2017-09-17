<?php

namespace App\Http\Controllers;

use App\Model\AreaModel;
use App\Service\AreaService;

class AreaController extends Controller
{

    public function saveProvinces(){
        $areaService = new AreaService();
        $areas = $areaService->getProvince();

        $saveArea = [];
        if($areas){
            foreach ($areas as $area){
                $temp = [];
                $temp['level'] = 1;
                $temp['name']  = $area['fullname'];
                $temp['area_id'] = $area['id'];
                $saveArea[] = $temp;
            }
        }
        $areaModel  = new AreaModel();
        $areaModel->saveArea($saveArea);
    }

    public function saveCitys(){

        ini_set("max_execution_time", 3000);
        set_time_limit(3000);

        $areaModel  = new AreaModel();
        $provinces = $areaModel->selectAreasByLevel(AreaModel::LEVEL_PROVINCE);
        $areaService = new AreaService();

        foreach ($provinces as $province){
            $citys = $areaService->getCity($province->area_id);
            if($citys){
                $saveArea = [];
                foreach ($citys as $city){
                    $temp = [];
                    $temp['level'] = 2;
                    $temp['name'] = $city['fullname'];
                    $temp['parent_id'] = $province->area_id;
                    $temp['area_id'] = $city['id'];
                    $saveArea[] = $temp;
                }
                $areaModel->saveArea($saveArea);
            }

            sleep(3);
        }

        die('success');
    }


    public function getProvince(){
        $areaModel  = new AreaModel();
        $provinces = $areaModel->selectAreasByLevel(AreaModel::LEVEL_PROVINCE);
        if($provinces){
            $data = [];
            foreach ($provinces as $province){
                $temp = [];
                $temp['id'] = $province->area_id;
                $temp['name'] = $province->name;
                $data[] = $temp;
            }
            return $this->return_json('success', $data);
        }else{
            return $this->return_json('error', '未查询到数据');
        }
    }

    public function getCity($provinceId){

        if(!$provinceId || !is_numeric($provinceId)){
            return $this->return_json('error', '参数错误');
        }

        $areaModel  = new AreaModel();
        $citys = $areaModel->selectAreasByLevel(AreaModel::LEVEL_CITY, $provinceId);
        if($citys){
            $data = [];
            foreach ($citys as $city){
                $temp = [];
                $temp['id'] = $city->area_id;
                $temp['name'] = $city->name;
                $data[] = $temp;
            }
            return $this->return_json('success', $data);
        }else{
            return $this->return_json('error', '未查询到数据');
        }

    }
}
