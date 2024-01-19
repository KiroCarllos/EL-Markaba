<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\GeneralController;
use App\Models\Area;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\University;
use App\Models\User;
use App\Models\StudentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class StudentDetailController extends Controller
{
    public function index(Request $request)
    {
        $user_student_details = User::Student()->whereHas("student_details")->latest()->get();
        $user_ids =User::pluck("id")->toArray();
        $user_dtails = StudentDetail::whereNotIn("user_id",$user_ids)->delete();
        return view('dashboard.user_student_details.index', compact('user_student_details'));
    }//end of index

    public
    function create()
    {
        $areas = Area::all();
        $universities = $this->getAllUniversities();
        return view('dashboard.user_student_details.create',compact("areas","universities"));

    }//end of create

    public
    function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'national_id' => 'required|string|size:14',
            'graduated_at' => ['required', 'date_format:Y'],
            'image' => 'required|mimes:jpeg,png,jpg|max:4096',
            'gender' => 'required|in:male,female',
            "prior_experiences" => ["nullable", "array"],
            "courses" => ["nullable", "array"],
            "address" => ["required", "string"],
            'education' => 'in:high,else',
            'else_education' => 'nullable',
            'faculty_id' => 'nullable',
            'area_id' => 'nullable',

        ]);


        $userData = $request->only(["name", "mobile", "email"]);
        $userData["password"] = Hash::make($request->password);
        try {
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ], [
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "role" => "student",
                "status" => "active",
            ]);
            deleteOldFiles("uploads/student/" . $user->id . "/profile");
            if ($request->image) {
                $user->update(["image" => uploadImage($request->image, "uploads/student/" . $user->id . "/profile")]);
            }
            $user->attachRole('student');
            $studentData = $request->only(["gender", "national_id","education", "area_id","faculty_id", "graduated_at", "major","prior_experiences", "else_education","courses", "address"]);

            $studentData = StudentDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $studentData);

            DB::commit();
            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.student_details.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception);
        }
    }//end of store

    public function edit($id)
    {
        $areas = Area::all();
        $userStudentDetail = User::whereId($id)->with("student_details")->first();
        if (is_null($userStudentDetail->student_details->faculty)){
            $faculties=[];
        }else{
            $faculties = $this->getFacultyByUniversityById($userStudentDetail->student_details->faculty->university_id);
        }
        $universities = $this->getAllUniversities();

        return view('dashboard.user_student_details.edit', compact('userStudentDetail',"universities","faculties",'areas'));
    } //end of user

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => ['required', Rule::unique('users',"email")->ignore($request->user_id)],
            'name' => 'required|string',
            'mobile' => ['required',"size:11", Rule::unique('users','mobile')->ignore($request->user_id)],
            'password' => 'nullable',
            'national_id' => 'required|string|size:14',
            'area_id' => 'nullable',
            'graduated_at' => ['nullable', 'date_format:Y'],
            'image' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'gender' => 'required|in:male,female',
            "prior_experiences" => ["nullable", "array"],
            "courses" => ["nullable", "array"],
            "address" => ["nullable", "string"],
            'major' => ["nullable", "string"],
            'else_education' => ["nullable", "string"],
        ]);

        $userData = $request->only(["name","mobile","email","status"]);
        if ($request->has("password") && !is_null($request->password)){
            $userData["password"] = Hash::make($request->password);
        }
        try {
            DB::beginTransaction();
            $user = User::query()->whereId($id)->first();
            if (($user->status != "active" && $request->status == "active" )){
                $result = send_fcm([$user->device_token],__("site.markz_el_markaba"),__("site.your_account_activated_can_make_login_now"),"posts",$user);
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.your_account_activated_can_make_login_now"),
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            $user->update($userData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/student/" . $user->id . "/profile");
                $user->update(["image" => uploadImage($request->image, "uploads/student/" . $user->id . "/profile")]);
            }
            $studentDetails = StudentDetail::whereUserId($id)->first();
            $studentData = $request->only(["gender", "faculty_id","else_education","major",'area_id',"national_id", "graduated_at","prior_experiences", "courses", "address"]);
            $studentData["enable_update"] = $request->has("enable_update") && !is_null($request->enable_update) && $request->enable_update == "on"? 1:0;
            if (($request->has("enable_update") && $request->enable_update == "on") && !$studentDetails->enable_update){
                $result = send_fcm([$user->device_token],__("site.markz_el_markaba"),__("site.you_now_able_to_edit_your_profile_data"),"dashboard",$user);
                Notification::create([
                    "type" => "dashboard",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.you_now_able_to_edit_your_profile_data"),
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            if ($studentDetails->enable_update && !($request->has("enable_update"))){
                $result = send_fcm([$user->device_token],__("site.markz_el_markaba"),__("site.you_now_not_able_to_edit_your_profile_data"),"dashboard",$user);
                Notification::create([
                    "type" => "dashboard",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.you_now_not_able_to_edit_your_profile_data"),
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            $studentDetails->update($studentData);
            if ($request->has("notify") && !is_null($request->notify)) {
                $result = send_fcm([$user->device_token],__("site.markz_el_markaba"),$request->notify,"posts",$user);
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => $request->notify,
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            if ($request->has("message") && !is_null($request->message)) {
                $admin = User::where("role" ,"super_admin")->where("status","active")->first();
                $chat = ChatMessage::query()->create([
                    "message" => $request->message,
                    "from_user_id" =>$admin->id ,
                    "to_user_id" => $user->id,
                    "created_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
                    "updated_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
                ]);

                $data["id"] = $chat->id;
                $data["direct"] = "left";
                $data["name"] = "Super Admin";
                $data["image"] = "http://el-markaba.kirellos.com/uploads/student/28/profile/student_profile_image_1690881214.";
                $data["message"] = $request->message;
                $data["status"] = "notReaded";
                $data["sent_at"] =Carbon::now()->timezone('Africa/Cairo')->diffForHumans();

                $result = send_fcm([$user->device_token],__("site.markz_el_markaba"),$request->message,"receiveMessage",$data);
                Notification::create([
                    "type" => "receiveMessage",
                    "title" => __("site.markz_el_markaba"),
                    "body" => $request->message,
                    "read" => "0",
                    "model_id" => $chat->id,
                    "model_json" => $chat,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
                DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.student_details.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
        }
    }//end of update

    public
    function destroy($id)
    {
        $userStudent = User::find($id);
        $userStudent->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.student_details.index');

    }//end of destroy


    public function export(){
        return Excel::download(new StudentExport(), 'students.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}//end of controller
