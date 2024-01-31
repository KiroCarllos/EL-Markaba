<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobOfficeDetail;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class JobOfficeController extends Controller
{
    // job Office login
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
        if ($user->role == "job_office" || $user->role == "super_admin") {
            if ($user->status == "active") {
                $user->update(["auth_token" => $token, "device_token" => $request->device_token]);
                return api_response(1, __("site.job_office successfully login"), $user);
            } else {
                $msg = "Sorry Your Account is " . $user->status . " now";
                return api_response(0, __("site." . $msg));
            }
        } else {
            return api_response(0, __("site.Sorry Your Account Not Be Company"), "");
        }
    }

    // job Office register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            "father_name" => "required|string",
            "church_name" => "required|string",
            "father_mobile" => "required|string|size:11",
            "amen_name" => "required|string",
            "amen_mobile" => "required|string|size:11",
            'logo' => 'required|mimes:jpeg,png,jpg|max:4096',
            'amen_national_image' => 'required|mimes:jpeg,png,jpg|max:4096',
        ]);
        $userData = $request->only(["name", "mobile", "email", "device_token"]);
        $userData["password"] = Hash::make($request->password);
        try {
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ], [
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "role" => "company",
                "device_token" => $userData["device_token"],
            ]);
            deleteOldFiles("uploads/job_office/" . $user->id . "/logo");
            if ($request->logo) {
                $user->update(["image" => uploadImage($request->logo, "uploads/job_office/" . $user->id . "/logo")]);
            }

            $user->attachRole('job_office');

            $jobOfficeData = $request->only(["father_name", "church_name", "father_mobile", "amen_name", "amen_mobile"]);
            $company = JobOfficeDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $jobOfficeData);
            deleteOldFiles("uploads/job_office/" . $user->id . "/amen_national_image");
            if ($request->amen_national_image) {
                $company->update(["amen_national_image" => uploadImage($request->amen_national_image, "uploads/job_office/" . $user->id . "/amen_national_image")]);
            }
            DB::commit();
            return api_response(1, __("site.job_office created successfully wait admins for approve"));
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception->getMessage());
        }

    }

    // job Office profile
    public function profile()
    {
        $user = User::query()->where("id", auth("api")->id())->with("office_details")->first();
        $user->setAttribute("notificationCount", Notification::where("user_id", auth("api")->id())->where("read", "0")->count());
        return api_response(1, __("site.profile office get successfully"), $user);
    }

    // job Office logout
    public function logout()
    {
        $user = auth("api")->user();
        $user->update(["auth_token" => null]);
        return api_response(1, __("site.office signOut successfully"));
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            "password" => ["required", "confirmed"]
        ]);
        $user = auth("api")->user();
        if (Hash::check($request->password, $user->password)) {
            $user->update(["email" => rand(1000, 9999) . "#" . $user->email, "mobile" => rand(1000, 9999) . "#" . $user->mobile]);
            $user->delete();
            return api_response(1, __("site.account deleted successfully"));
        }
        return api_response(0, __("site.Sorry Wrong Password"));

    }

    public function getMyJobs()
    {
        $jobs = Job::where('user_id', auth("api")->id())
            ->active()
            ->latest()
            ->paginate(6);
        return api_response(1, "", $jobs);
    }

    public function addJob(Request $request)
    {
        $request->validate([
            'title_en' => 'required',
            'title_ar' => 'required',
            'description_en' => 'required',
            'description_ar' => 'required',
            'work_type' => 'required|in:part_time,full_time',
            'work_hours' => 'nullable',
            'contact_email' => 'required|email',
            'address' => 'required',
            'job_type' => 'required|in:from_company,online',

            'location' => 'nullable',
            'expected_salary_from' => 'required|numeric',
            'expected_salary_to' => 'required|numeric',
        ]);
        $request_data = $request->only(['title_en', 'job_type', 'description_en', 'description_ar', 'title_ar', 'work_type', 'contact_email', 'address', 'location', 'expected_salary_from', 'expected_salary_to']);
        $request_data["status"] = "pending";
        $request_data["user_id"] = auth("api")->id();
        $request_data["work_hours"] = $request->has("work_hours") && !is_null($request->work_hours) ? $request->work_hours : 8;
        try {
            DB::beginTransaction();
            $job = Job::query()->firstOrCreate($request_data);
            DB::commit();
            return api_response(1, __('site.added_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return  api_response(0,$exception->getMessage());
        }
    }

    public function updateJob(Request $request)
    {
        $request->validate([
            'job_id' => ['required', Rule::exists("job_tables", "id")->where("user_id", auth("api")->id())],
            'title_en' => 'nullable',
            'title_ar' => 'nullable',
            'description_en' => 'nullable',
            'description_ar' => 'nullable',
            'work_type' => 'nullable|in:part_time,full_time',
            'job_type' => 'nullable|in:from_company,online',

            'work_hours' => 'nullable',
            'contact_email' => 'nullable|email',
            'address' => 'nullable',
            'location' => 'nullable',
            'expected_salary_from' => 'nullable|numeric',
            'expected_salary_to' => 'nullable|numeric',
        ]);
        try {
            DB::beginTransaction();
            $job = Job::find($request->job_id);
            if (is_null($job)) {
                return api_response(0, "sorry job is inValid");
            }
            $request_data = $request->only(['title_ar', "job_type", 'title_en', 'description_ar', 'description_en', 'work_type', "work_hours", 'contact_email', 'address', 'location', 'expected_salary_from', 'expected_salary_to']);
            if (count($request_data) == 0) {
                return api_response(0, "please fill data for update");
            }
            $request_data["status"] = ($request->has("status") && $request->status !== "enough") || ($request->has("status") && $request->status !== "deleted") ? $request->status : $job->status;
            if ($job->status == "active") {
                $request_data["status"] = "pending";
            }
            $job->update($request_data);
            DB::commit();
            return api_response(1, __('site.updated_successfully'), $request_data);
        } catch (\Exception $exception) {
            DB::rollBack();
            return  api_response(0,$exception->getMessage());

        }
    }

    public function deleteJob(Request $request)
    {
        $request->validate([
            "job_id" => ["required", "numeric", Rule::exists("job_tables", "id")],
            "status" => "required|in:enough,deleted"
        ]);
        $job = Job::whereId($request->job_id)->whereUserId(auth("api")->id())->first();
        if (is_null($job)) {
            return api_response(0, __("site.sorry job is inValid"));
        }
        $job->status = $request->status;
        $job->save();
        $msg = "your job " . $request->status . " successfully";
        return api_response(1, __("site." . $msg));
    }

    public function getJobApplications(Request $request)
    {
        $request->validate([
            "job_id" => ["required", "numeric", Rule::exists("job_tables", "id")->where("user_id", auth("api")->id())],
        ]);
        $job = Job::find($request->job_id);
        if ($job->status == "enough") {
            $jobApplications = JobApplication::where("job_id", $request->job_id)->whereIn("status", ["confirmed"])->with([
                "user" => function ($query) {
                    $query->with("student_details");
                }
            ])->paginate(15);
        } elseif ($job->status == "active") {
            $jobApplications = JobApplication::where("job_id", $request->job_id)->whereIn("status", ["confirmed", 'notConfirmed', "inProgress"])->with([
                "user" => function ($query) {
                    $query->with("student_details");
                }
            ])->paginate(15);
        } else {
            $jobApplications = JobApplication::where("job_id", $request->job_id)->whereIn("status", ["confidfsdfsdfsdfsdfrmed"])->with([
                "user" => function ($query) {
                    $query->with("student_details");
                }
            ])->paginate(15);
        }
        return api_response(1, "", $jobApplications);
    }

    public function notifications()
    {
        $notifications = Notification::where("user_id", auth("api")->id())->latest()->paginate(15);
        return api_response(1, "", $notifications);
    }

    public function updateApplicationStatus(Request $request)
    {
        $request->validate([
            "application_id" => ["required", "numeric", Rule::exists("job_applications", "id")],
            "status" => ["required", "in:confirmed,notConfirmed"]
        ]);
        $myJobIds = Job::where("user_id", auth("api")->id())->pluck("id")->toArray();
        $application = JobApplication::find($request->application_id);
        if (in_array($application->job_id, $myJobIds)) {
            if ($application->status == "confirmed" || $application->status == "notConfirmed") {
                return api_response(1, "");
            }
            if ($request->status == "confirmed") {

                $result = send_fcm([$application->user->device_token], __("site.markz_el_markaba"), __("site.congratulations_your_application_has_been_accepted"), "posts", $application);
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.congratulations_your_application_has_been_accepted"),
                    "read" => "0",
                    "model_id" => $application->id,
                    "model_json" => $application,
                    "user_id" => $application->user->id,
                    "fcm" => $result,
                ]);
            } else {
                $result = send_fcm([$application->user->device_token], __("site.markz_el_markaba"), __("site.we_really_sorry_your_application_has_been_rejected"), "posts", $application);
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.we_really_sorry_your_application_has_been_rejected"),
                    "read" => "0",
                    "model_id" => $application->id,
                    "model_json" => $application,
                    "user_id" => $application->user->id,
                    "fcm" => $result,
                ]);
            }
            $application->update(["status" => $request->status]);
            return api_response(1, "");

        }
        return api_response(0, __("site.something_went_wrong"));

    }

    public function updateNotification(Request $request)
    {
        $request->validate([
            "notification_id" => ["required", "numeric", Rule::exists("notifications", "id")->where("user_id", auth("api")->id())],
            "read" => ["required", "in:0,1"]
        ]);
        $notification = Notification::find($request->notification_id);
        $notification->update(["read" => $request->read]);
        $notificationCount = Notification::where("user_id", auth("api")->id())->where("read", "0")->count();
        return api_response(1, __('site.updated_successfully'), $notificationCount);
    }

}//end of controller
