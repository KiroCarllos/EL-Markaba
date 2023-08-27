<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    // company login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = ["email" => $request->email, "password" => $request->password];
        if (!$token = auth("api")->attempt($credentials)) {
            return api_response(0, __("site.These credentials do not match our records."), "");
        }
        $user = User::where("email", $request->email)->first();
        if ($user->role == "company" || $user->role == "super_admin") {
            if ($user->status == "active") {
                $user->update(["auth_token" => $token]);
                return api_response(1, __("site.company successfully login"), $user);
            } else {
                $msg = "Sorry Your Account is " . $user->status . " now";
                return api_response(0, __("site.".$msg));
            }

        } else {
            return api_response(0, __("site.Sorry Your Account Not Be Company"), "");
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
            "administrator_name" => "required|string",
            "administrator_mobile" => "required|string|size:11",
            'bio' => 'required|string',
            'created_date' => 'required|date',
            'address' => 'required|string',
            'logo' => 'required|mimes:jpeg,png,jpg|max:4096',
            'commercial_record_image' => 'required|mimes:jpeg,png,jpg|max:4096',
            'tax_card_image' => 'required|mimes:jpeg,png,jpg|max:4096',
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
                "role" => "company",
            ]);
            deleteOldFiles("uploads/companies/" . $user->id . "/logo");
            if ($request->logo) {
                $user->update(["image" => uploadImage($request->logo, "uploads/companies/" . $user->id . "/logo/" . generateBcryptHash($user->id) . "/logo")]);
            }
            $user->attachRole('company');
            $companyData = $request->only(["administrator_name", "administrator_mobile", "bio", "address"]);
            $companyData["created_date"] = Carbon::parse($request->created_date)->toDateString();
            $company = CompanyDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $companyData);
            deleteOldFiles("uploads/companies/" . $user->id . "/commercial_record");
            if ($request->commercial_record_image) {
                $company->update(["commercial_record_image" => uploadImage($request->commercial_record_image, "uploads/companies/" . $user->id . "/commercial_record/" . generateBcryptHash($user->id) . "/commercial_record")]);
            }
            deleteOldFiles("uploads/companies/" . $user->id . "/tax_card");
            if ($request->tax_card_image) {
                $company->update(["tax_card_image" => uploadImage($request->tax_card_image, "uploads/companies/" . $user->id . "/tax_card/" . generateBcryptHash($user->id) . "/tax_card")]);
            }
            DB::commit();
            return api_response(1, __("site.company created successfully wait admins for approve"));
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception);
        }

    }

    // company profile
    public function profile()
    {
        $user = User::query()->where("id", auth("api")->id())->with("company_details")->first();
        return api_response(1, __("site.profile company get successfully"), $user);
    }

    // company logout
    public function logout()
    {
        $user = auth("api")->user();
        $user->update(["auth_token" => null]);
        return api_response(1, __("site.company signOut successfully"));
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
            return api_response(1, __("site.company deleted successfully"));
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
        $request_data = $request->only(['title_en','job_type', 'description_en','description_ar','title_ar', 'work_type', 'contact_email', 'address', 'location', 'expected_salary_from', 'expected_salary_to']);
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
            dd($exception);
        }
    }

    public function updateJob(Request $request){
        $request->validate([
            'job_id' => ['required',Rule::exists("jobs","id")->where("user_id",auth("api")->id())],
            'title_en' => 'nullable',
            'title_ar' => 'nullable',
            'description_en' => 'nullable',
            'description_ar' => 'nullable',
            'work_type' => 'nullable|in:part_time,full_time',
            'job_type' => 'required|in:from_company,online',

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
            if (is_null($job)){
                return  api_response(0,"sorry job is inValid");
            }
            $request_data = $request->only(['title_ar',"job_type",'title_en', 'description_ar','description_en', 'work_type',"work_hours", 'contact_email', 'address', 'location', 'expected_salary_from','expected_salary_to']);
            if (count($request_data) == 0){
                return api_response(0, "please fill data for update");
            }
            $request_data["status"] =($request->has("status") && $request->status !== "enough") || ($request->has("status") &&$request->status !==  "deleted") ? $request->status : $job->status ;
            if ($job->status == "active"){
                $request_data["status"] = "pending";
            }
            $job->update($request_data);
            DB::commit();
            return api_response(1, __('site.updated_successfully'),$request_data);
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
        }
    }
    public function deleteJob(Request $request){
        $request->validate([
           "job_id"=> ["required","numeric",Rule::exists("jobs","id")],
           "status"=> "required|in:enough,deleted"
        ]);
        $job = Job::whereId($request->job_id)->whereUserId(auth("api")->id())->first();
        if (is_null($job)){
            return  api_response(0,__("site.sorry job is inValid"));
        }
        $job->status =$request->status;
        $job->save();
        $msg = "your job ".$request->status ." successfully";
        return api_response(1, __("site.".$msg));
    }

}//end of controller
