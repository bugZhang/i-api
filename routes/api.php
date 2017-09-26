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


Route::get('/area/getProvince', 'AreaController@getProvince');
Route::get('/area/getCity/{id}', 'AreaController@getCity');
Route::get('/bank/search/{bankCode}/{province}/{city}/{keyword}/{page?}', 'BankController@getBanks');

Route::get('/wx/get-session-key/{code}', 'WxLoginController@getSessionKey');
Route::get('/wx/get-user/{openid}', 'WxLoginController@getUser');
Route::post('/wx/save-user', 'WxLoginController@saveUser');

Route::get('/wx/bank/collect/get', 'WxBankCollectController@getUserCollect');  //获取用户收藏
Route::get('/wx/bank/collect/save/{bankcode}', 'WxBankCollectController@saveUserCollect');
Route::get('/wx/bank/collect/delete/{bankcode}', 'WxBankCollectController@deleteUserCollect');
Route::get('/wx/img/get/random', 'AreaController@getRandomImg');

Route::get('/kxxx/backup/{sc}', 'BackupController@sendMail');