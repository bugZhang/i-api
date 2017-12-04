<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class KelenewsModel extends Model
{

    protected $connection = 'kelenews';
    public $timestamps = false;

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

}
