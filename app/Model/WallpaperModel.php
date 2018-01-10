<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WallpaperModel extends Model{

    protected $table = 'wallpaper';
    public $timestamps = false;

    const TYPE_GIRL = 1;
    const TYPE_BOY = 2;
    const TYPE_SCENE = 3;

    public function addOne($type, $filename){

    }

    public function deleteOne($id){

    }

    public function addImpression($id){

    }

    public function getListByType($type, $page, $sort = 'new'){

        if(!$page || $page < 1){
            $page = 1;
        }
        $limit = 6;
        $offset = $limit * ($page - 1);
        $condition = ['type', '=', $type];

        $orderByColumn = $sort == 'hot' ? 'impression' : 'add_time';
        $wallpapers = $this->where($condition)->select()
            ->offset($offset)
            ->limit($limit)
            ->orderBy($orderByColumn, 'desc')
            ->get();

        return $wallpapers && $wallpapers->count() > 0 ? $wallpapers : false;
    }

    public function getNewListByType(){

    }

    public function getHotListByType(){

    }

    public function getOne($id){

    }

}