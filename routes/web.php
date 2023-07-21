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

use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return redirect()->route('dashboard.welcome');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/terms_and_conditions', function (){
    return view("terms");
});
Route::get('/deleteAccount', function (){
    return view("deleteAccount");
})->name("getDeleteAccount");
Route::post('/deleteAccount', function (\Illuminate\Http\Request $request ){
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        // Authentication successful
        auth()->user()->delete();
        return Redirect::back()->withInput()->withErrors(['credentials' => 'Your Account Has been Deleted Successfully']);
    }
    // Authentication failed
    return Redirect::back()->withInput()->withErrors(['credentials' => 'Invalid credentials']);

})->name("deleteAccount");
