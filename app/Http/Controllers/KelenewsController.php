<?php

namespace App\Http\Controllers;

use App\Model\KelenewsModel;
use Illuminate\Http\Request;

class KelenewsController extends Controller
{
    public function getPosts(){
        $kelenewsModel  = new KelenewsModel();

        $posts = $kelenewsModel->selectAllPostsByPage(1);
        var_dump($posts);
    }

    public function getPost($postId){

        if(!$postId){
            return $this->return_json('error', 'id不能为空');
        }
        $kelenewsModel  = new KelenewsModel();

        $post = $kelenewsModel->selectPostById($postId);

        if(count($post)){
            $post->post_content = preg_replace('/<img([^>]+>)/i', '<img width="100%" $1', $post->post_content);
        }

        return $this->return_json('success', $post->toArray());

    }


}
