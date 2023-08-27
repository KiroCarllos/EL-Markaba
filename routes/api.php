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



Route::group(['namespace' => 'Api',"middleware"=>["changeLanguage"]], function () {
    // General
    Route::post('getUniversities', 'GeneralController@getUniversities')->name("getUniversities");
    Route::post('getFacultyByUniversity', 'GeneralController@getFacultyByUniversity')->name("getFacultyByUniversity");
    Route::post('getSlider', 'GeneralController@getSlider');
    Route::post('about', 'GeneralController@about');
    Route::post('getSetting', 'GeneralController@getSetting');


    // Forget Password
    Route::post('sendMailForReset', 'GeneralController@sendMailForReset');
    Route::post('confirmTokenForReset', 'GeneralController@confirmTokenForReset');

});



// Job
Route::group(['prefix' => "job",'namespace' => 'Api'], function () {


});
Route::group(['prefix' => "job",'middleware'=>['auth:api',"check_auth","changeLanguage"],'namespace' => 'Api'], function () {
    Route::post('getAvailJobs', 'JobController@getAvailJobs');

});
// end Job routes

/// // company
Route::group(['prefix' => "company",'namespace' => 'Api',"middleware" =>["changeLanguage"]], function () {
    Route::post('login', 'CompanyController@login');
    Route::post('register', 'CompanyController@register');

});
Route::group(['prefix' => "company",'middleware'=>['auth:api',"check_auth","changeLanguage"],'namespace' => 'Api'], function () {
    Route::post('profile', 'CompanyController@profile');
    Route::post('logout', 'CompanyController@logout');
    Route::post('deleteAccount', 'CompanyController@deleteAccount');
    Route::post('resetPassword', 'GeneralController@resetPassword');


    // Jobs
    Route::post('getMyJobs', 'CompanyController@getMyJobs');
    Route::post('getJobDetails', 'JobController@getJobDetails');
    Route::post('addJob', 'CompanyController@addJob');
    Route::post('updateJob', 'CompanyController@updateJob');
    Route::post('deleteJob', 'CompanyController@deleteJob');
    Route::post('getJobApplications', 'CompanyController@getJobApplications');

    Route::post('notifications', 'CompanyController@notifications');

});
// end company routes


// student
Route::group(['prefix' => "student",'namespace' => 'Api',"middleware" => ["changeLanguage"]], function () {
    Route::post('login', 'StudentController@login');
    Route::post('register', 'StudentController@register');

    // POSTS
    Route::post('getPosts', 'StudentController@getPosts');

});
Route::group(['prefix' => "student",'middleware'=>['auth:api',"check_auth","changeLanguage"],'namespace' => 'Api'], function () {
    Route::post('profile', 'StudentController@profile');
    Route::post('logout', 'StudentController@logout');
    Route::post('deleteAccount', 'StudentController@deleteAccount');
    Route::post('resetPassword', 'GeneralController@resetPassword');

    // POSTS
    Route::post('replyPost', 'StudentController@replyPost');

    // Trainings
    Route::post('getTrainings', 'StudentController@getTrainings');
    Route::post('applyTraining', 'StudentController@applyTraining');
    Route::post('myTrainings', 'StudentController@myTrainings');
    Route::post('confirmAppliedTraining', 'StudentController@confirmAppliedTraining');
    Route::post('cancelAppliedTraining', 'StudentController@cancelAppliedTraining');


    Route::post('notifications', 'StudentController@notifications');

    // job
    Route::post('applyJob', 'StudentController@applyJob');
    Route::post('myJobs', 'StudentController@myJobs');
    Route::post('getJobDetails', 'StudentController@getJobDetails');
    Route::post('cancelAppliedJob', 'StudentController@cancelAppliedJob');

});
// end student routes
