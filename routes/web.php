<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/haha/test', 'TestController@test');
Route::get('/haha/test/admin/{type}/{page?}', 'WallpaperController@managerView');
Route::get('/haha/test/papa', 'WallpaperController@deleteWallpaper');
Route::post('/haha/test/gogo', 'WallpaperController@uploadWallpaper');

Route::get('/tbk/test', 'TaobaoController@test');
Route::get('/tbk/items/get', 'TaobaoController@getItems');
Route::get('/tbk/share/pwd', 'TaobaoController@sharePwd');
Route::get('/tbk/favourites/{favouriteId}/{page?}', 'TaobaoController@getFavouriteItems');
Route::get('/tbk/coupon/get', 'TaobaoController@getCouponItems');
Route::get('/tbk/item/{id}', 'TaobaoController@getItemInfo');
Route::get('/tbk/pwd/query', 'TaobaoController@queryPwdFromPwd');

Route::get('/kelenews/impression/increat/{postId}', 'KelenewsController@increatCount');
Route::get('/kelenews/post/list/{page}', 'KelenewsController@getPostsFromCache');
Route::get('/kelenews/post/get/{postId}', 'KelenewsController@getPost');
Route::get('/kelenews/post/flush/{k}/{postId?}', 'KelenewsController@flushPosts');


Route::get('subbank/area/save', 'SubBankController@saveArea');

