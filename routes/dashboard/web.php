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

        });//end of dashboard routes
    });


