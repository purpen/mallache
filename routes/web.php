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

Route::group(['namespace' => 'Web'], function () {
    Route::post('/jdCallbackUrl', 'JdCallBackController@callUrl');
});
Route::get('/designQuotationDown', 'Api\V1\DesignQuotationController@DownQuotationPDF')->middleware('jwt.auth');

Route::get('/test', 'TestController@index');
Route::get('/test/create', 'TestController@create');
Route::post('/test/store', 'TestController@store');
