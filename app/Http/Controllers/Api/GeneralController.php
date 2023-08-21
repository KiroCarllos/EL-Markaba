<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Major;
use App\Models\ResetPassword;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\University;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class GeneralController extends Controller
{
   public function getUniversities(){
       $universities= $this->getAllUniversities();
       return api_response(1,"",$universities);
   }
    public function getFacultyByUniversity(Request $request){
       $request->validate([
           "university_id" => ["required","numeric",Rule::exists("universities","id")]
       ]);
       $faculties = Faculty::query()->select("id","name_en","name_ar")->where("university_id",$request->university_id)->get();
        foreach ($faculties as $faculty){
            $name = app()->getLocale() == "ar" ?$faculty->name_ar:$faculty->name_en;
            $faculty->setAttribute("name",$name);
            $faculty->makeHidden(["name_en","name_ar"]);
        }
        return api_response(1,"",$faculties);
    }

    public function getSlider(Request $request){
        $request->validate([
            "role" => ["required","in:super_admin,admin,student,job_company,company"]
        ]);
        $sliders = Slider::active()->whereJsonContains("role",$request->role)->latest()->pluck("image")->toArray();
        return api_response(1,"",$sliders);
    }
    public function about(){
        $data["details_ar"] = "مركز المركبة هو مركز للارشاد الوظيفي وريادة الاعمال بتاسيس ورعاية نيافة الحبر الجليل الانبا بافلي اسقف المنتزه و الشباب بالاسكندرية لخدمة شباب وشابات الاسكندرية ومساعدتهم لدخول سوق العمل و القبول بافضل الشركات و تحديد مجالات العمل المناسبة لهم بعد عملية التقييم الشخصي و التدريب حسب الاحتياجات الشخصية كما يهتم المركز بتقديم الاستشارات لرواد الاعمال الشباب.";
        $data["details_en"] = "Markz El Markaba is a center for career guidance and entrepreneurship, established and sponsored by His Grace Bishop Pavli, Bishop of Montazah and Youth in Alexandria, to serve the youth of Alexandria and help them enter the labor market, accept the best companies, and determine the appropriate fields of work for them after the process of personal evaluation and training according to personal needs. The center also cares Providing advice to young entrepreneurs.";
        $data["urls"][0]["type_en"] = "whatsApp";
        $data["urls"][0]["type_ar"] = "الواتساب";
        $data["urls"][0]["value"] = "whatsApp";
        $data["urls"][1]["type_en"] = "Facebook";
        $data["urls"][1]["type_ar"] = "الفيسبوك";
        $data["urls"][1]["value"] = "https://www.facebook.com/CareerGuidanceCenter2018/";
        $data["urls"][2]["type_en"] = "Email";
        $data["urls"][2]["type_ar"] = "الايميل";
        $data["urls"][2]["value"] = "mailto:elmarkaba.careerguidance@gmail.com";
        $data["urls"][3]["type_en"] = "Mobile";
        $data["urls"][3]["type_ar"] = "الموبيل";
        $data["urls"][3]["value"] = "tel:+01288834652";


       return api_response(1,"",$data);
    }

    public function getSetting(){
       return api_response(1,"",Setting::first());
    }

    // Forget Password
    public function sendMailForReset(Request $request){
        $request->validate([
            "email" => ["required","email","string",Rule::exists("users","email")]
        ]);
        try {
            $token = rand(10000,50000);
            ResetPassword::where("email", $request->email)->delete();
            ResetPassword::create([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            $userEmail = $request->email;
            $data["token"] = $token;
            $data["email"] = $userEmail;
            Mail::send("mail.reset_password", ['data' => $data], function ($message) use ($userEmail) {
                $message->from("elmarkaba.careerguidance@gmail.com", 'El Markaba');
                $message->to($userEmail);
                $message->subject('El Markaba Reset Password');
            });
            if (Mail::failures()) {
                return api_response(0, "sorry,some thing went error In Mail please try again");
            }
            return api_response(1, __("site.Sent successfully Please, Check Your Inbox or Spam"));
        } catch (\Exception $exception) {
            return api_response(0, $exception->getMessage());
        }
    }
    public function confirmTokenForReset(Request $request){
        $request->validate([
            "email" => ["required","email","string",Rule::exists("password_resets","email"),Rule::exists("users","email")],
            "token" => ["required","numeric",Rule::exists("password_resets")],
            'password' => 'required|confirmed|min:8',
        ]);
        try {
            $user_data = ResetPassword::where("email", $request->email)->where("token",$request->token)->first();
            if (is_null($user_data)) {
                return api_response(0, __("site.sorry, invalid token code"));
            }
            $user = User::where("email",$request->email)->first();
            $user->update(["password" => Hash::make($request->password)]);
            ResetPassword::where("email", $request->email)->delete();
            return api_response(1, __("site.Password reset successfully"));
        } catch (\Exception $exception) {
            return api_response(0, $exception->getMessage());
        }
    }
    public function resetPassword(Request $request){
        $request->validate([
            'password' => 'required|confirmed|min:8',
        ]);
        try {
            $user = User::whereId(auth("api")->id())->first();
            $user->update(["password" => Hash::make($request->password)]);
            return api_response(1, __("site.Password reset successfully"));
        } catch (\Exception $exception) {
            return api_response(0, $exception->getMessage());
        }
    }
}//end of controller
