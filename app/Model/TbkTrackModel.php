<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TbkTrackModel extends Model{

    protected $table = 'tbk_track_info';
//    public $timestamps = false;

    const TYPE_CLICK = 'click';
    const TYPE_IMPRESSION = 'impression';

    const ACTION_1 = '按关键词查询';
    const ACTION_2 = '生成淘口令';
    const ACTION_3 = '收藏';
    const ACTION_4 = '分享';
    const ACTION_5 = '通过淘口令查询';

    public function addTrackInfo($trackInfo){

        if(empty($trackInfo)){
            return 0;
        }

        return $this->insert($trackInfo);
    }

}