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
});



Route::group(['namespace' => 'Api'], function () {

    // General
    Route::post('getUniversities', 'GeneralController@getUniversities')->name("getUniversities");
    Route::post('getFacultyByUniversity', 'GeneralController@getFacultyByUniversity')->name("getFacultyByUniversity");
    Route::post('getMajorByFaculty', 'GeneralController@getMajorByFaculty')->name("getMajorByFaculty");


    Route::post('getPosts', 'GeneralController@getPosts');

});


// Job
Route::group(['prefix' => "job",'namespace' => 'Api'], function () {


});
Route::group(['prefix' => "job",'middleware'=>['auth:api',"check_auth"],'namespace' => 'Api'], function () {
    Route::post('getAvailJobs', 'JobController@getAvailJobs');
    Route::post('getJobDetails', 'JobController@getJobDetails');
});
// end Job routes

/// // company
Route::group(['prefix' => "company",'namespace' => 'Api'], function () {
    Route::post('login', 'CompanyController@login');
    Route::post('register', 'CompanyController@register');

});
Route::group(['prefix' => "company",'middleware'=>['auth:api',"check_auth"],'namespace' => 'Api'], function () {
    Route::post('profile', 'CompanyController@profile');
    Route::post('logout', 'CompanyController@logout');
});
// end company routes


// student
Route::group(['prefix' => "student",'namespace' => 'Api'], function () {
    Route::post('login', 'StudentController@login');
    Route::post('register', 'StudentController@register');

});
Route::group(['prefix' => "student",'middleware'=>['auth:api',"check_auth"],'namespace' => 'Api'], function () {
    Route::post('profile', 'StudentController@profile');
    Route::post('logout', 'StudentController@logout');
});
// end student routes
