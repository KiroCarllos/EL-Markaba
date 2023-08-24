<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\GeneralController;
use App\Models\University;
use App\Models\User;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class StudentDetailController extends Controller
{
    public function index(Request $request)
    {
        $user_student_details = User::Student()->whereHas("student_details")->latest()->paginate(50);
        $user_ids =User::pluck("id")->toArray();
        $user_dtails = StudentDetail::whereNotIn("user_id",$user_ids)->delete();
        return view('dashboard.user_student_details.index', compact('user_student_details'));
    }//end of index

    public
    function create()
    {
        return view('dashboard.user_student_details.create');

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
            'image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'gender' => 'required|in:male,female',
            "prior_experiences" => ["required", "array"],
            "courses" => ["required", "array"],
            "address" => ["required", "string"],
            'major_id' => ["required", Rule::exists('majors',"id")->whereIn('id', Major::pluck('id')->toArray())->whereNotIn('id', ['not_from_above'])],
            'else_major' => 'required_if:major_id,not_from_above',
            'else_education' => 'nullable',

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
            $studentData = $request->only(["gender", "national_id", "graduated_at", "prior_experiences", "else_education","courses", "address"]);
            if($request->has("major_id")){
                $major_id = (int) $request->major_id;
                if (is_int($major_id)){
                    $studentData["major_id"] = (int) $request->major_id;
                }else{
                    $studentData["major_id"] = (int) $request->else_major;
                }
            }
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
        $userStudentDetail = User::whereId($id)->with("student_details")->first();
        if (is_null($userStudentDetail->student_details->faculty)){
            $faculties=[];
        }else{
            $faculties = $this->getFacultyByUniversityById($userStudentDetail->student_details->faculty->university_id);
        }
        $universities = $this->getAllUniversities();

        return view('dashboard.user_student_details.edit', compact('userStudentDetail',"universities","faculties"));
    } //end of user

    public function update(Request $request, $id)
    {
        $request->validate([
            'email' => ['required', Rule::unique('users',"email")->ignore($request->user_id)],
            'name' => 'required|string',
            'mobile' => ['required',"size:11", Rule::unique('users','mobile')->ignore($request->user_id)],
            'password' => 'nullable',
            'national_id' => 'required|string|size:14',
            'graduated_at' => ['nullable', 'date_format:Y'],
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'gender' => 'required|in:male,female',
            "prior_experiences" => ["nullable", "array"],
            "courses" => ["nullable", "array"],
            "address" => ["nullable", "string"],
            'major' => ["nullable", "string"],
            'else_education' => ["nullable", "string"],
        ]);
        dd($request->all());
        $userData = $request->only(["name","mobile","email","status"]);
        if ($request->has("password") && !is_null($request->password)){
            $userData["password"] = Hash::make($request->password);
        }
        try {
            DB::beginTransaction();
            $user = User::query()->whereId($id)->first();
            $user->update($userData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/student/" . $user->id . "/profile");
                $user->update(["image" => uploadImage($request->image, "uploads/student/" . $user->id . "/profile")]);
            }
            $studentDetails = StudentDetail::whereUserId($id)->first();

            $studentData = $request->only(["gender", "faculty_id","else_education","major","national_id", "graduated_at", "prior_experiences", "courses", "address"]);

            $studentDetails->update($studentData);
            if ( $user->status == "pending" && $request->status = "active"){
                dd($user->status,$request->status);
                $recipients = [$user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.your_account_activated_can_make_login_now"),"posts");
            }
            if ($request->has("notify") && !is_null($request->notify)) {
                $recipients = [$user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),$request->notify,"posts");
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

}//end of controller
