<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class KelenewsModel extends Model
{

    protected $connection = 'kelenews';
    public $timestamps = false;

    private $re_key_view_count = 'kelenew_view_count';

    public function selectAllPostsByPage($pageNum = 1){

        return $this->from('wp_posts')
            ->where([
                ['post_status', '=', 'publish'],
                ['post_type', '=', 'post']
            ])
            ->select()->limit(10)->latest('post_date_gmt')->get();
    }

    public function selectPostById($postId){
        return $this->from('wp_posts')->where([
            ['ID', '=', $postId]
        ])->first();
    }

    public function increatViewCount($postId){
        return Redis::hIncrBy($this->re_key_view_count, $postId, 1);
    }

    public function getViewCount($postId){
        return Redis::hGet($this->re_key_view_count, $postId);
    }

    public function getViewCountGroup($postIds){
        return Redis::hMGet($this->re_key_view_count, $postIds);
    }

}
