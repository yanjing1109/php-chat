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

Route::post('news/list', 'NewsController@getList');
Route::post('news/detail', 'NewsController@getDetail');
Route::post('news/modifyNews', 'NewsController@modifyNews');
