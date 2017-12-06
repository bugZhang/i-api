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

    public function selectAllTags(){

        $tags = $this->from('wp_terms')
            ->join('wp_term_taxonomy', 'wp_term_taxonomy.term_id' ,'=', 'wp_terms.term_id')
            ->where('wp_term_taxonomy.taxonomy', '=', 'post_tag')
            ->select()
            ->get();
        return $tags;

    }


    public function isVideoFormat($postId){

        $termId = $this->from('wp_term_relationships')
            ->join('wp_terms', 'wp_term_relationships.term_taxonomy_id', '=', 'wp_terms.term_id')
            ->where([
                ['wp_terms.name', '=' , 'post-format-video'],
                ['wp_term_relationships.object_id', '=', $postId]
            ])
            ->select('term_id')
            ->get();

        return count($termId) ? true : false;
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
