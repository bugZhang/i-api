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


Route::get('/service/area/saveProvinces', 'AreaController@saveProvinces');
Route::get('/service/area/saveCitys', 'AreaController@saveCitys');

Route::get('/api/area/getProvince', 'AreaController@getProvince');
Route::get('/api/area/getCity/{id}', 'AreaController@getCity');
Route::get('/api/bank/search/{bankCode}/{province}/{city}/{keyword}/{page?}', 'BankController@getBanks');

Route::get('/api/wx/get-session-key/{code}', 'WxLoginController@getSessionKey');

