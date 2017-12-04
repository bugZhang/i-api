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

}
