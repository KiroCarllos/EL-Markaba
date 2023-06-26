<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware'=>['auth:api',"check_auth"],'namespace' => 'Api'], function () {
    Route::post('profile', 'AuthController@profile');

});
Route::group(['namespace' => 'Api'], function () {
    Route::post('login', 'AuthController@login');

});
