<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']],
    function () {

        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function () {

            Route::get('/', 'WelcomeController@index')->name('welcome');

            //user routes
            Route::resource('users', 'UserController')->except(['show']);
            Route::resource('companies', 'CompanyController');
            Route::post('companies/updateStatus', 'CompanyController@updateStatus')->name("companies.updateStatus");

            Route::resource('user_student_details', 'UserStudentDetailController');
            Route::resource('jobs', 'JobController');

        });//end of dashboard routes
    });


