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

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
//    $recipients = ["dxWUemmZSkm7zQdmpxWrNJ:APA91bELXt2_xq-oZXJfepfzBgFtMtt_U_PbP94g_1O00myoi7yxLha3uXrXsSsI2BInC3bJ33n1QOPASDlALzqIStutDSGKfhdwQF6-etB1L3YXEryd7D-_Dmd3s83k0Pz0cG2avz3d","c1lsSlYgQDiAZVDTBwD2W2:APA91bHXFurrWA-iZIiyRO3xcRFoDsipBv1_St1ds7-k3agcelUzfL02wsCFJDlFfvSTWpiT_oiBMLmujQ8QQJZfKQWxaxhwVT_fvOdJzO56l2lTxmfZyGGAZgb2Llp8AW0mAVxruT8-"];
//    $s = send_fcm($recipients,"مركز المركبة","لقد تم قبولك في تدريب ال TOT ","trainings",["id"=>1,"title"=>"test"]);
//    dd($s);
    return redirect()->route('dashboard.welcome');
});
Route::get('/sendFcm', function () {

//    $recipients = User::where("role","student")->whereNotNull("device_token")->get();
//
//    foreach ($recipients as $recipient){
//        send_fcm([$recipient->device_token],"من جوجل بلاي تقدر تحديث","لقد تم تفعيل الوظايف لتطبيق الشركه والطالب","chat");
//    }


    $recipients = ["d8ocrNveSUGBSur9bip1m7:APA91bFG_H2EfgEHdDhFDUY4cGJ99dtEpmz9XEGMXSZPZ1Ks6b72tMtztYHffHthyvs0FdgRvb7zV_R61sqegv9QSTdXgvreM9n_61KC7aT_5cOzM9-fCtqnbmKP5HD5mNkiVB6IiAa8"];
    $message = "ماشي مفيش مشكله";
    $s = sendFcm([$recipients],"مركز المركبة",$message,"chat",$message);
    dd($s);


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
