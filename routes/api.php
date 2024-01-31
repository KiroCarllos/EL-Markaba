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
    Route::post('getAreas', 'GeneralController@getAreas')->name("getAreas");
    Route::post('getUniversities', 'GeneralController@getUniversities')->name("getUniversities");
    Route::post('getFacultyByUniversity', 'GeneralController@getFacultyByUniversity')->name("getFacultyByUniversity");
    Route::post('getSlider', 'GeneralController@getSlider');
    Route::post('about', 'GeneralController@about');
    Route::post('getSetting', 'GeneralController@getSetting');


    // Forget Password
    Route::post('sendMailForReset', 'GeneralController@sendMailForReset');
    Route::post('confirmTokenForReset', 'GeneralController@confirmTokenForReset');


    // services
    Route::post('checkExpiredJobs', 'ServiceController@checkExpiredJobs');

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
    Route::post('updateApplicationStatus', 'CompanyController@updateApplicationStatus');
    Route::post('updateNotification', 'CompanyController@updateNotification');

});
// end company routes


/// Job Office
Route::group(['prefix' => "jobOffice",'namespace' => 'Api',"middleware" =>["changeLanguage"]], function () {
    Route::post('login', 'JobOfficeController@login');
    Route::post('register', 'JobOfficeController@register');
});

Route::group(['prefix' => "jobOffice",'middleware'=>['auth:api',"check_auth","changeLanguage"],'namespace' => 'Api'], function () {
    Route::post('profile', 'JobOfficeController@profile');
    Route::post('logout', 'JobOfficeController@logout');
    Route::post('deleteAccount', 'JobOfficeController@deleteAccount');
    Route::post('resetPassword', 'GeneralController@resetPassword');


    // Jobs
    Route::post('getMyJobs', 'JobOfficeController@getMyJobs');
    Route::post('getJobDetails', 'JobController@getJobDetails');
    Route::post('addJob', 'JobOfficeController@addJob');
    Route::post('updateJob', 'JobOfficeController@updateJob');
    Route::post('deleteJob', 'JobOfficeController@deleteJob');
    Route::post('getJobApplications', 'JobOfficeController@getJobApplications');

    Route::post('notifications', 'JobOfficeController@notifications');
    Route::post('updateApplicationStatus', 'JobOfficeController@updateApplicationStatus');
    Route::post('updateNotification', 'JobOfficeController@updateNotification');

});





// student
Route::group(['prefix' => "student",'namespace' => 'Api',"middleware" => ["changeLanguage"]], function () {
    Route::post('login', 'StudentController@login');
    Route::post('register', 'StudentController@register');

    // POSTS
    Route::post('getPosts', 'StudentController@getPosts');

});
Route::group(['prefix' => "student",'middleware'=>['auth:api',"check_auth","changeLanguage"],'namespace' => 'Api'], function () {
    Route::post('profile', 'StudentController@profile');
    Route::post('updateProfile', 'StudentController@updateProfile');

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
    Route::post('updateNotification', 'StudentController@updateNotification');

    // job
    Route::post('applyJob', 'StudentController@applyJob');
    Route::post('myJobs', 'StudentController@myJobs');
    Route::post('getJobDetails', 'StudentController@getJobDetails');
    Route::post('cancelAppliedJob', 'StudentController@cancelAppliedJob');

});
// end student routes


// Fathers
Route::group(['prefix' => "father",'namespace' => 'Api',"middleware" =>["changeLanguage"]], function () {
    Route::post('login', 'FatherController@login');
    Route::post('register', 'FatherController@register');

});
Route::group(['prefix' => "father",'middleware'=>['auth:api',"check_auth","changeLanguage"],'namespace' => 'Api'], function () {
    Route::post('profile', 'FatherController@profile');
    Route::post('logout', 'FatherController@logout');
    Route::post('deleteAccount', 'FatherController@deleteAccount');
    Route::post('resetPassword', 'GeneralController@resetPassword');


    Route::post('notifications', 'FatherController@notifications');
    Route::post('updateNotification', 'FatherController@updateNotification');


    // Suggestion Api
    Route::post('searchStudent', 'FatherController@searchStudent');

});



// Chat Auth
Route::group(['prefix' => "chat",'namespace' => 'Api',"middleware" => ['auth:api',"check_auth","changeLanguage"]], function () {
    Route::post('admins', 'ChatController@admins');
    Route::post('sendMessage', 'ChatController@sendMessage');
    Route::post('getMyMessages', 'ChatController@getMyMessages');

});
