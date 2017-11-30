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


Route::get('/tbk/test', 'TaobaoController@test');
Route::get('/tbk/items/get', 'TaobaoController@getItems');
Route::get('/tbk/share/pwd', 'TaobaoController@sharePwd');
Route::get('/tbk/favourites/{favouriteId}/{page?}', 'TaobaoController@getFavouriteItems');
Route::get('/tbk/coupon/get', 'TaobaoController@getCouponItems');
