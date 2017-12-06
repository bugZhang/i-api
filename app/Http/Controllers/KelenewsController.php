<?php

namespace App\Http\Controllers;

use App\Model\KelenewsModel;
use Illuminate\Http\Request;
use Symfony\Component\Console\Helper\Helper;

class KelenewsController extends Controller
{
    public function getPosts($page){
        $kelenewsModel  = new KelenewsModel();
        $posts = $kelenewsModel->selectAllPostsByPage($page ? $page : 1);
        if(count($posts)){

            $allTags = $kelenewsModel->selectAllTags()->toArray();
            foreach ($posts as $post){
                $post->post_excerpt = $this->getPostExcerpt($post);
                $post->post_date = date('Y-m-d', strtotime($post->post_date));

                $postTags = $kelenewsModel->selectPostTags($post->ID);
                if($postTags){
                    $post_tags = $this->getPostTagsName($postTags->toArray(), $allTags);
                    $post->post_tags = implode(',', $post_tags);
                }
            }
            return $this->return_json('success', $posts->toArray());
        }else{
            return $this->return_json('error', '未查询到数据');
        }
    }

    public function getPost($postId){

        if(!$postId){
            return $this->return_json('error', 'id不能为空');
        }
        $kelenewsModel  = new KelenewsModel();

        $post = $kelenewsModel->selectPostById($postId);

        if(count($post)){
            $post->post_content = preg_replace('/(<img.*?)width=".*?"/i', '$1', $post->post_content);
            $post->post_content = preg_replace('/(<img.*?)style=".*?"/i', '$1', $post->post_content);
            $post->post_content = preg_replace('/<img([^>]+>)/i', '<img width="100%" $1', $post->post_content);
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

        return $this->return_json('success', $post->toArray());

    }

    public function increatCount($postId){
        if($postId){
            $kelenewsModel  = new KelenewsModel();
            $count = $kelenewsModel->increatViewCount($postId);
            return $this->return_json('success', $count);
        }else{
            return $this->return_json('error', '参数错误');
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

}
