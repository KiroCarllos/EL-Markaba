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
//    $recipients = ["dxWUemmZSkm7zQdmpxWrNJ:APA91bELXt2_xq-oZXJfepfzBgFtMtt_U_PbP94g_1O00myoi7yxLha3uXrXsSsI2BInC3bJ33n1QOPASDlALzqIStutDSGKfhdwQF6-etB1L3YXEryd7D-_Dmd3s83k0Pz0cG2avz3d","c1lsSlYgQDiAZVDTBwD2W2:APA91bHXFurrWA-iZIiyRO3xcRFoDsipBv1_St1ds7-k3agcelUzfL02wsCFJDlFfvSTWpiT_oiBMLmujQ8QQJZfKQWxaxhwVT_fvOdJzO56l2lTxmfZyGGAZgb2Llp8AW0mAVxruT8-"];
//    $s = send_fcm($recipients,"مركز المركبة","لقد تم قبولك في تدريب ال TOT ","myTraining",["id"=>1,"title"=>"test"]);
//    dd($s);
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
