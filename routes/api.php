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