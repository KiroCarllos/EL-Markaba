<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']],
    function () {

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

            Route::get('/', 'WelcomeController@index')->name('welcome');

            //user routes
            Route::resource('users', 'UserController')->except(['show']);

            // companies
            Route::resource('companies', 'CompanyController');
            Route::post('companies/updateStatus', 'CompanyController@updateStatus')->name("companies.updateStatus");

            //students
            Route::resource('student_details', 'StudentDetailController');
            Route::resource('jobs', 'JobController');

            Route::resource('posts', 'PostController');
            Route::resource('sliders', 'SliderController');

            Route::resource('trainings', 'TrainingController');
            Route::get('trainings/applications/{id}', 'TrainingController@applications')->name("trainings.applications");
            Route::get('trainings/applications/{id}/edit', 'TrainingController@editApplication')->name("trainings.applications.edit");
            Route::put('trainings/applications/{id}/update', 'TrainingController@updateApplication')->name("trainings.applications.update");
            Route::delete('trainings/applications/{id}/destroy', 'TrainingController@deleteApplication')->name("trainings.applications.destroy");


            Route::get('jobs/applications/{id}', 'JobController@applications')->name("jobs.applications");
            Route::get('jobs/applications/{id}/edit', 'JobController@editApplication')->name("jobs.applications.edit");
            Route::put('jobs/applications/{id}/update', 'JobController@updateApplication')->name("jobs.applications.update");
            Route::delete('jobs/applications/{id}/destroy', 'JobController@deleteApplication')->name("jobs.applications.destroy");

        });//end of dashboard routes
    });


