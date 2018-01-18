<?php

namespace App\Http\Controllers;

use App\Model\WallpaperModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class WallpaperController extends Controller
{
    //

    public function getList(Request $request)
    {
        $page = $request->page;
        $type = $request->type;
        if($type != WallpaperModel::TYPE_GIRL
            && $type != WallpaperModel::TYPE_BOY
            && $type != WallpaperModel::TYPE_SCENE
        ){
            return $this->return_json('error');

        }

        $model = new WallpaperModel();
        $wallpapers = $model->getListByType($type, $page, '', 6);

        if ($wallpapers) {
            $wallpapers = $wallpapers->toArray();
            foreach ($wallpapers as $wallpaper){
                $wallpaper['src'] = 'http://wallpaper.kelenews.com/image/wallpaper/' .$type . '/' . $wallpaper['filename'] . '-slim.img';
//                $wallpaper['src'] = URL::asset('image/wallpaper/' . $type . '/' . $wallpaper['filename']);
                $data[] = $wallpaper;
            }
            return $this->return_json('success', $data);
        }else{
            return $this->return_json('error');
        }
    }

    public function getWallpaper($type, $wid){
        $model  = new WallpaperModel();
        $wallpaper = $model->getOne($wid);
        if($wallpaper){
            $wallpaper   = $wallpaper->toArray();
            $wallpaper['src'] = 'http://wallpaper.kelenews.com/image/wallpaper/' .$type . '/' . $wallpaper['filename'];
//            $wallpaper['src'] = URL::asset('image/wallpaper/' . $type . '/' . $wallpaper['filename']);
            return $this->return_json('success', $wallpaper);
        }else{
            return $this->return_json('error');
        }


    }

    public function uploadWallpaper(Request $request){

        $file = $request->file('wallpaper');
        $type = $request->type;
        $pwd = $request->pwd;

        if(!$pwd || $pwd != env('MY_WALLPAPER_KEY')){
           return $this->return_json('error');
        }

        if ($file) {
            $mimeType = $file->getMimeType();
            if($mimeType == 'image/gif'){
                $extension = 'gif';
            }elseif ($mimeType == 'image/jpeg'){
                $extension = 'jpg';
            }elseif ($mimeType == 'image/png'){
                $extension = 'png';
            }else{
                return $this->return_json('error', '文件格式错误');
            }

            if($file->getSize() > (1024 * 8000)){
                return $this->return_json('error', '文件太大');
            }
            $filename = uniqid() . '.' . $extension;
            $hash_code = hash_file('md5', $file->getRealPath());
            $path = $request->wallpaper->storeAs('', $filename, 'public_' . $type . '_uploads');
            if($path){
                $model = new WallpaperModel();
                $model->addOne($type, $filename, $hash_code);
            }
        }
        return redirect('/haha/test/admin/' . $type);
    }

    public function deleteWallpaper(Request $request){
        $id = $request->wid;
        $type = $request->type;
        $pwd = $request->pwd;
        if(!$pwd || $pwd != env('MY_WALLPAPER_KEY')){
            return $this->return_json('error');
        }

        if($id && $type){
            $model = new WallpaperModel();
            $wallpaper = $model->getOne($id);
            if($model->deleteOne($id)){
                Storage::disk('public_' . $type . '_uploads')->delete($wallpaper->filename);
                return $this->return_json('success');
            }else{
                return $this->return_json('error');
            }
        }else{
            return $this->return_json('error');
        }
    }

    public function managerView(Request $request){
        $type = $request->type;
        $page = $request->page;
        $model  = new WallpaperModel();
        $wallpapers = $model->getListByType($type, $page);
        return view('wallpaper', ['wallpapers' => $wallpapers, 'type'=>$type]);
    }

}
