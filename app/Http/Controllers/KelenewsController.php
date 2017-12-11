<?php

namespace App\Http\Controllers;

use App\Model\KelenewsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Console\Helper\Helper;

class KelenewsController extends Controller
{

    private $cache_kelenews_list = 'kelenews_page_';
    private $cache_kelenews_post = 'kelenews_post_';

    /**
     * @param $page 页数
     */
    public function getPostsFromCache($page){
        $page = ($page && $page > 0) ? $page : 1;
        $post_list_cache_key = $this->cache_kelenews_list . $page;

        if(Cache::has($post_list_cache_key)){
            $arrPosts = Cache::get($post_list_cache_key);
            if($arrPosts){
                return $this->return_json('success', $arrPosts);
            }else{
                return $this->return_json('error', '未查询到数据');
            }
        }else{
            $kelenewsModel  = new KelenewsModel();
            $posts = $kelenewsModel->selectAllPostsByPage($page);

            if(count($posts)){

                $allTags = $kelenewsModel->selectAllTags()->toArray();
                foreach ($posts as $post){
                    $post->post_excerpt = $this->getPostExcerpt($post);
                    $post->post_date = date('Y-m-d', strtotime($post->post_date));

                    preg_match('/<img.*?src="(.*?)"/i', $post->post_content, $matchs);
                    if($matchs){
                        $post->thumb_pic = $matchs[1];
                    }

                    $postTags = $kelenewsModel->selectPostTags($post->ID);
                    if($postTags){
                        $post_tags = $this->getPostTagsName($postTags->toArray(), $allTags);
                        $post->post_tags = implode(',', $post_tags);
                    }

                    if($viewCount = $kelenewsModel->getViewCount($post->ID)){
                        $post->view_count = $viewCount;
                    }
                }

                $arrPosts = $posts->toArray();
                Cache::put($post_list_cache_key, $arrPosts, 60 * 24);   //minutes  缓存一天
                return $this->return_json('success', $arrPosts);
            }else{
                return $this->return_json('error', '未查询到数据');
            }
        }
    }

    public function getPost($postId){

        if(!$postId){
            return $this->return_json('error', 'id不能为空');
        }
        $cache_key = $this->cache_kelenews_post . $postId;

        if(Cache::has($cache_key)){
            $arrPost = Cache::get($cache_key);
            if($arrPost){
                return $this->return_json('success', $arrPost);
            }else{
                return $this->return_json('error', '未查询到数据');
            }
        }else{
            $kelenewsModel  = new KelenewsModel();
            $post = $kelenewsModel->selectPostById($postId);

            if(count($post)){
                $post->post_content = preg_replace('/(<img.*?)width=".*?"/i', '$1', $post->post_content);
                $post->post_content = preg_replace('/(<img.*?)style=".*?"/i', '$1', $post->post_content);
                $post->post_content = preg_replace('/<img([^>]+>)/i', '<img width="100%" $1', $post->post_content);
            }else{
                return $this->return_json('error', '未查找到数据');
            }

            $post->post_date = date('Y-m-d', strtotime($post->post_date));

            if($post->is_video = $kelenewsModel->isVideoFormat($postId)){
                preg_match('/<video.*?src="(.*?)"/i', $post->post_content, $matchs);
                if($matchs){
                    $post->video_src = $matchs[1];
                }
                $post->post_content = preg_replace('/<video.*?video>/i', '', $post->post_content);
            }
            $postTags = $kelenewsModel->selectPostTags($postId);
            if(count($postTags)){
                $allTags = $kelenewsModel->selectAllTags()->toArray();
                $post_tags = $this->getPostTagsName($postTags->toArray(), $allTags);
                if($postTags){
                    $post->post_tags = implode(',', $post_tags);
                }
            }

            $kelenewsModel->increatViewCount($postId);

            if($viewCount = $kelenewsModel->getViewCount($post->ID)){
                $post->view_count = $viewCount;
            }

            $arrPost = $post->toArray();
            Cache::put($cache_key, $arrPost, 60 * 24);   //minutes
            return $this->return_json('success', $arrPost);
        }

    }

    private function getPostTagsName($postTags, $allTags){
        $allTags = array_column($allTags, 'name', 'term_id');
        $tagNames = [];
        foreach ($postTags as $tag){
            if(isset($allTags[$tag['term_taxonomy_id']])){
                $tagNames[] = $allTags[$tag['term_taxonomy_id']];
            }
        }
        return $tagNames;
    }

    private function getPostExcerpt($post, $length = 300){
        if(!$post || !$post->post_content){
            return '';
        }
        $content = strip_tags($post->post_content);
        return Helper::substr($content, 0, $length);
    }


    public function flushPosts($k, $postId = ''){

        if($k != env('KELENEWS_FLUSH_TOKEN')){
            return $this->return_json('error', "what're u doing?");
        }

        if($postId){
            if(Cache::has($this->cache_kelenews_post . $postId)){
                Cache::forget($this->cache_kelenews_post . $postId);
            }
        }else{
            $kelenewsModel  = new KelenewsModel();
            $pages = $kelenewsModel->selectPageCount();
            if($pages){
                for($i = 1; $i <= $pages; $i++){
                    if(Cache::has($this->cache_kelenews_list . $i)){
                        Cache::forget($this->cache_kelenews_list . $i);
                    }
                }
            }
        }
        return $this->return_json('success', '操作成功');
    }

}
