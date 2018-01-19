<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WallpaperModel extends Model{

    protected $table = 'wallpaper';
    public $timestamps = false;

    const TYPE_GIRL = 'girl';
    const TYPE_BOY = 'boy';
    const TYPE_SCENE = 'scene';

    public function addOne($type, $filename, $hash){
        if(!$type || !$filename || !$hash){
            return false;
        }else{
            return $this->insert(['type' => $type, 'filename' => $filename, 'hash_code' => $hash]);
        }
    }

    public function deleteOne($id){
        return $this->where('id', '=', $id)->delete();
    }

    public function addImpression($id){
        return $this->where('id', '=', $id)->increment('impression');
    }

    public function getListByType($type, $page, $sort = 'new', $limit = 6){

        if(!$page || $page < 1){
            $page = 1;
        }
        $offset = $limit * ($page - 1);
        $condition[] = ['type', '=', $type];

        $orderByColumn = $sort == 'hot' ? 'impression' : 'add_time';
        $wallpapers = $this->where($condition)->select()
            ->offset($offset)
            ->limit($limit)
            ->orderBy($orderByColumn, 'desc')
            ->get();

        return $wallpapers && $wallpapers->count() > 0 ? $wallpapers : false;
    }

    public function getOne($id){
        if(!$id){
            return false;
        }
        $wallpapers = $this->where('id', '=', $id)->select()->get();
        return $wallpapers->count() > 0 ? $wallpapers[0] : false;
    }

}