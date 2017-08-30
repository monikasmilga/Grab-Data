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

Route::get('/grabbed', ['as' => 'app.data.index', 'uses' => 'GDDataGrabbingController@index']);

Route::get('/result', ['as' => 'app.data.onePageData', 'uses' => 'GDDataGrabbingController@grabAllData']);

Route::get('/resultmore', ['as' => 'app.data.grabMoreData', 'uses' => 'GDDataGrabbingController@grabMoreData']);


