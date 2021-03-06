<?php

namespace App\Http\Controllers;

use App\Model\AreaModel;
use App\Service\AreaService;

class AreaController extends Controller
{

    public function saveProvinces(){
        if(!env('IS_ENABLE_SERVICE')){
            return 0;
        }
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
        if(!env('IS_ENABLE_SERVICE')){
            return 0;
        }

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

    public function getProvinceAndCity(){
        $areaModel  = new AreaModel();
        $provinces = $areaModel->selectAreasByLevel(AreaModel::LEVEL_PROVINCE);
        if($provinces){
            $data = [];
            foreach ($provinces as $province){
                $citys = $areaModel->selectAreasByLevel(AreaModel::LEVEL_CITY, $province->area_id);
                if($citys){
                    foreach ($citys as $city) {
                        $data[$province->name][] = $city->name;
                    }
                }

            }
            return $this->return_json('success', $data);
        }else{
            return $this->return_json('error', '未查询到数据');
        }
    }

    public function getRandomImg(){

        $urls = [
            'http://images.kelenews.com/wx/imags/random/6869ca96dd72d1717e4e98dfef0e4ce6.jpg',
            'http://images.kelenews.com/wx/imags/random/d42eb358fd228431704c68a53b3c3b2c.jpg',
            'http://images.kelenews.com/wx/imags/random/05ee20a4977e564b60f2fe38538294f1.jpg',
            'http://images.kelenews.com/wx/imags/random/3a1e235e04a14cf5a8f0e7ed330e08c9.jpg',
            'http://images.kelenews.com/wx/imags/random/1opasdf23.jpg',
        ];
        if(count($urls) == 1){
            $url = $urls[0];
        }else{
            $seed = rand(0, (count($urls) - 1));
            $url = $urls[$seed];
        }

        return redirect($url);
//        $type = pathinfo($url, PATHINFO_EXTENSION);
//        $image = file_get_contents($url);
//        header("Content-type: image/" . $type);
//        echo $image;
//        die();
    }
}
