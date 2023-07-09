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
    Route::post('getSlider', 'GeneralController@getSlider');


    // Forget Password
    Route::post('sendMailForReset', 'GeneralController@sendMailForReset');
    Route::post('confirmTokenForReset', 'GeneralController@confirmTokenForReset');

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

    // Jobs
    Route::post('getMyJobs', 'CompanyController@getMyJobs');
    Route::post('addJob', 'CompanyController@addJob');
    Route::post('updateJob', 'CompanyController@updateJob');
    Route::post('deleteJob', 'CompanyController@deleteJob');
});
// end company routes


// student
Route::group(['prefix' => "student",'namespace' => 'Api'], function () {
    Route::post('login', 'StudentController@login');
    Route::post('register', 'StudentController@register');

    // POSTS
    Route::post('getPosts', 'StudentController@getPosts');

});
Route::group(['prefix' => "student",'middleware'=>['auth:api',"check_auth"],'namespace' => 'Api'], function () {
    Route::post('profile', 'StudentController@profile');
    Route::post('logout', 'StudentController@logout');

    // POSTS
    Route::post('replyPost', 'StudentController@replyPost');

    // Trainings
    Route::post('getTrainings', 'StudentController@getTrainings');
    Route::post('applyTraining', 'StudentController@applyTraining');
    Route::post('myTrainings', 'StudentController@myTrainings');
    Route::post('confirmAppliedTraining', 'StudentController@confirmAppliedTraining');
});
// end student routes
