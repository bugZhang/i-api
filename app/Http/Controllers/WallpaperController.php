<?php

namespace App\Http\Controllers;

use App\Model\WallpaperModel;
use Illuminate\Http\Request;

class WallpaperController extends Controller
{
    //

    public function getList(Request $request)
    {

        $model = new WallpaperModel();
        $wallpapers = $model->getListByType(WallpaperModel::TYPE_GIRL);

        if ($wallpapers) {
            $wallpapers = $wallpapers->toArray();
            foreach ($wallpapers as $i => $wallpaper) {
                if ($i % 2 == 0) {
                    $left[] = $wallpaper;
                } else {
                    $right[] = $wallpaper;
                }
            }
            return $this->return_json('success', ['left' => $left, 'right' => $right]);
        }

    }

}
