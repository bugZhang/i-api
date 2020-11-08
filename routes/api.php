<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('new-bank')->middleware(['checkNewBankSession'])->group(function(){
    Route::get('/search/{bankCode}/{province}/{keyword}/{page?}', 'BankController@getNewBanks');
    Route::get('/get-session-key/{code}', 'WxNewBankLoginController@getSessionKey');
    Route::get('/session/check/psid', 'WxNewBankLoginController@checkPsid');
    Route::post('/save-user', 'WxNewBankLoginController@saveUser');
    Route::get('/collect/save/{bankcode}', 'WxBankCollectController@saveUserCollect');
    Route::get('/collect/delete/{bankcode}', 'WxBankCollectController@deleteUserCollect');
    Route::get('/collect/get', 'WxBankCollectController@getNewBankUserCollect');  //获取用户收藏
    Route::get('/banner-img/get', 'BankController@getBannerImgs');  //获取用户收藏

});

Route::prefix('wx-tudou')->group(function(){
    Route::post('/', 'WxTudouController@getMsg');
});


Route::middleware(['checkwxsessoin'])->group(function(){
    Route::get('/area/getProvince', 'AreaController@getProvince');
    Route::get('/area/getCity/{id}', 'AreaController@getCity');
    Route::get('/bank/search/{bankCode}/{province}/{keyword}/{page?}', 'BankController@getBanksV2');

    Route::get('/wx/bank/zhi/get', 'WxLoginController@getMyZhi');  //
    Route::get('/wx/get-session-key/{code}', 'WxLoginController@getSessionKey');
    Route::get('/wx/session/check/psid', 'WxLoginController@checkPsid');
    Route::get('/wx/get-user/{openid}', 'WxLoginController@getUser');
    Route::post('/wx/save-user', 'WxLoginController@saveUser');

    Route::get('/wx/bank/collect/get', 'WxBankCollectController@getUserCollectV2');  //获取用户收藏
    Route::get('/wx/bank/collect/save/{bankcode}', 'WxBankCollectController@saveUserCollect');
    Route::get('/wx/bank/collect/delete/{bankcode}', 'WxBankCollectController@deleteUserCollect');
});


Route::get('/wx/img/get/random', 'AreaController@getRandomImg');
Route::post('/wx/contact/get', 'WxContactController@getMsg');

Route::get('/kxxx/backup/{sc}', 'BackupController@sendMail');

Route::get('/kelenews/impression/increat/{postId}', 'KelenewsController@increatCount');
Route::get('/kelenews/post/list/{page}', 'KelenewsController@getPostsFromCache');
Route::get('/kelenews/post/get/{postId}', 'KelenewsController@getPost');
Route::get('/kelenews/post/flush/{k}/{postId?}', 'KelenewsController@flushPosts');
Route::post('/kelenews/impression/get', 'KelenewsController@getImpressionCount');


Route::get('/wallpaper/list/{type}/{page?}', 'WallpaperController@getList');
Route::get('/wallpaper/get/{type}/{wid}', 'WallpaperController@getWallpaper');
Route::get('/wallpaper/impression/add/{wid}', 'WallpaperController@addImpression');

Route::post('/fun/router', 'RouterController@getRouter');

Route::get('/tbk/test', 'TaobaoController@test');
Route::get('/tbk/items/get', 'TaobaoController@getItems');
Route::post('/tbk/share/pwd', 'TaobaoController@sharePwd');
Route::get('/tbk/favourites/{favouriteId}/{page?}', 'TaobaoController@getFavouriteItems');
Route::post('/tbk/pwd/query', 'TaobaoController@queryPwdFromPwd');
Route::post('/tbk/keyword/search', 'TaobaoController@searchMaterial');

