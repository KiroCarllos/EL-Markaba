<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CompanyDetail;
use App\Models\FatherDetail;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\StudentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class FatherController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }
    // company login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_token' => 'nullable',
        ]);
        $credentials = ["email" => $request->email, "password" => $request->password];
        if (!$token = auth("api")->attempt($credentials)) {
            return api_response(0, __("site.These credentials do not match our records."), "");
        }
        $user = User::where("email", $request->email)->first();
        if ($user->role == "father" || $user->role == "super_admin") {
            if ($user->status == "active") {
                $user->update(["auth_token" => $token,"device_token"=>$request->device_token]);
                return api_response(1, __("site.father successfully login"), $user);
            } else {
                $msg = "Sorry Your Account is " . $user->status . " now";
                return api_response(0, __("site.".$msg));
            }
        } else {
            return api_response(0, __("site.Sorry Your Account Not Be Father"), "");
        }
    }

    // company register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            "area_id" => ["required",Rule::exists(Area::class,"id")],
            'logo' => 'required|mimes:jpeg,png,jpg|max:4096',
            'national_id_image' => 'required|mimes:jpeg,png,jpg|max:4096',
            'device_token' => 'nullable',
        ]);

        $userData = $request->only(["name", "mobile", "email","device_token"]);
        $userData["password"] = Hash::make($request->password);
        try {
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ], [
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "role" => "father",
                "device_token" => $userData["device_token"],
            ]);

            deleteOldFiles("uploads/fathers/" . $user->id . "/logo");
            if ($request->logo) {
                $user->update(["image" => uploadImage($request->logo, "uploads/fathers/" . $user->id . "/logo/" . generateBcryptHash($user->id) . "/logo")]);
            }
            $user->attachRole('father');
            $fatherDetails = FatherDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ]);
            deleteOldFiles("uploads/fathers/" . $user->id . "/national_image");
            if ($request->national_id_image) {
                $fatherDetails->update(["national_image" => uploadImage($request->national_id_image, "uploads/fathers/" . $user->id . "/national_image/" . generateBcryptHash($user->id) . "/national_image")]);
            }

            DB::commit();
            return api_response(1, __("site.father created successfully wait admins for approve"));
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception->getMessage());
        }

    }

    // company profile
    public function profile()
    {
        $user = User::query()->where("id", auth("api")->id())->with("father_details")->first();
        $user->setAttribute("notificationCount",  Notification::where("user_id",auth("api")->id())->where("read","0")->count());
        return api_response(1, __("site.profile father get successfully"), $user);
    }

    // company logout
    public function logout()
    {
        $user = auth("api")->user();
        $user->update(["auth_token" => null]);
        return api_response(1, __("site.father signOut successfully"));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            "password" => ["required","confirmed"]
        ]);
        $user = auth("api")->user();
        if (Hash::check($request->password,$user->password)){
            $user->update(["email"=>rand(1000,9999)."#".$user->email,"mobile"=>rand(1000,9999)."#".$user->mobile]);
            $user->delete();
            return api_response(1, __("site.father deleted successfully"));
        }
        return api_response(0, __("site.Sorry Wrong Password"));

    }



    public function notifications(){
        $notifications = Notification::where("user_id",auth("api")->id())->latest()->paginate(15);
        return api_response(1,"",$notifications);
    }

    public function updateNotification(Request $request){
        $request->validate([
            "notification_id" => ["required","numeric",Rule::exists("notifications","id")->where("user_id",auth("api")->id())],
            "read" => ["required","in:0,1"]
        ]);
        $notification = Notification::find($request->notification_id);
        $notification->update(["read" => $request->read]);
        $notificationCount =  Notification::where("user_id",auth("api")->id())->where("read","0")->count();
        return api_response(1,__('site.updated_successfully'),$notificationCount);
    }



    public function searchStudent(Request $request){

        $students = $this->studentRepository->search($request->search);
        return api_response(1,__('site.get_successfully'),$students);
    }

}//end of controller
