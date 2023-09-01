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

use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
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


    $recipients = ["d8ocrNveSUGBSur9bip1m7:APA91bFG_H2EfgEHdDhFDUY4cGJ99dtEpmz9XEGMXSZPZ1Ks6b72tMtztYHffHthyvs0FdgRvb7zV_R61sqegv9QSTdXgvreM9n_61KC7aT_5cOzM9-fCtqnbmKP5HD5mNkiVB6IiAa8","exrqk0ThSnmreYizaZFoPW:APA91bEe3lf_-E-TKmBspyaoQOQok8NzoR-ZkjhZq_onuLVfHbruoCuPq_lYAuTn7SpAah_rCihE6KgATc9wtmNQV14vIAeXAlxiBEwz6ghNd7bsIdPshfLan3Rrs2zwyYQitYeV_QQ-","cnUvEJ5RTmOzxqLaRFTOnF:APA91bGNTBV2klCJRWlIiHVMVogTBy7IEhzvamFLPFKCiRCXukKiE8AE4ikJrMUUCrbvelJ27reNCnJyLg40hx2XTZH0EqlT_UY6R80iU0vi8XNyHtUBNhXDLYZhWmIzhz1TQdDrKwH-"];
    $message = "ماشي مفيش مشكله";

    $chat = ChatMessage::query()->create([
        "message" => $message,
        "from_user_id" => 1,
        "to_user_id" => 28,
        "created_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
        "updated_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
    ]);

    $data["id"] = $chat->id;
    $data["direct"] = "left";
    $data["name"] = "Super Admin";
    $data["image"] = "http://el-markaba.kirellos.com/uploads/student/28/profile/student_profile_image_1690881214.";
    $data["message"] = $message;
    $data["status"] = "notReaded";
    $data["sent_at"] =Carbon::now()->timezone('Africa/Cairo')->diffForHumans();

    $s = send_fcm($recipients,"مركز المركبة",$message,"receiveMessage",$data);
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
