<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\Major;
use App\Models\User;
use App\Models\StudentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // student login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = ["email" => $request->email, "password" => $request->password];
        if (!$token = auth("api")->attempt($credentials)) {
            return api_response(0, "These credentials do not match our records.", "");
        }
        $user = User::where("email", $request->email)->first();
        if ($user->role == "student" || $user->role == "super_admin") {
            if ($user->status == "active") {
                $user->update(["auth_token" => $token]);
                return api_response(1, "student successfully login", $user);
            } else {
                return api_response(0, "Sorry Your Account is " . $user->status . " now");
            }

        } else {
            return api_response(0, "Sorry Your Account Not Be Student", "");
        }
    }

    // student register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'gender' => 'required|in:male,female',
            'national_id' => 'required|string|size:14',
            'major_id' => ["required", Rule::exists("majors", "id")],
            'graduated_at' => ['required', 'date_format:Y'],
            "prior_experiences" => ["required", "array"],
            "courses" => ["required", "array"],
            "address" => ["required", "string"],
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
            ]);
            deleteOldFiles("uploads/student/" . $user->id . "/profile");
            if ($request->image) {
                $user->update(["image" => uploadImage($request->image, "uploads/student/" . $user->id . "/profile")]);
            }
            $user->attachRole('student');
            $studentData = $request->only(["gender", "national_id", "major_id", "graduated_at", "prior_experiences", "courses", "address"]);
            $studentData = StudentDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $studentData);

            DB::commit();
            return api_response(1, "student created successfully wait admins for approve");
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception);
        }
    }

    // student profile
    public function profile()
    {
        $user = User::query()->where("id", auth("api")->id())->with("student_details")->first();
        return api_response(1, "profile student get successfully", $user);
    }

    // student logout
    public function logout()
    {
        $user = auth("api")->user();
        $user->update(["auth_token" => null]);
        return api_response(1, "student signOut successfully");
    }
}//end of controller
