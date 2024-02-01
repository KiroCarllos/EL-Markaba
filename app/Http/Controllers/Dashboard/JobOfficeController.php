<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\CompanyExport;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDetail;
use App\Models\JobOfficeDetail;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class JobOfficeController extends Controller
{
    public function index(Request $request)
    {
        $job_offices = User::JobOffice()->latest()->get();
        return view('dashboard.job_offices.index', compact('job_offices'));
    }//end of index

    public
    function create()
    {
        return view('dashboard.job_offices.create');

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            "father_name" => "required|string",
            "church_name" => "required|string",
            "father_mobile" => "required|string|size:11",
            "amen_name" => "required|string",
            "amen_mobile" => "required|string|size:11",
            'logo' => 'required|mimes:jpeg,png,jpg|max:4096',
            'amen_national_image' => 'required|mimes:jpeg,png,jpg|max:4096',
        ]);
        $userData = $request->only(["name","mobile","email"]);
        $userData["password"] = Hash::make($request->password);

        try {
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ], [
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "role" => "job_office",
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
            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.job_offices.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception->getMessage());
        }
    }//end of store

    public function edit($id)
    {
        $jobOffice = User::JobOffice()->whereId($id)->with("office_details")->first();
        if (is_null($jobOffice)){
            return  abort(404);
        }
        return view('dashboard.job_offices.edit', compact('jobOffice'));
    }//end of user

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'mobile' => ["required","string",'size:11',Rule::unique('users', 'mobile')->ignore($request->user_id)],
            'email' => ['required',"email",Rule::unique('users', 'email')->ignore($request->user_id)],
            'password' => 'nullable',
            "father_name" => "required|string",
            "church_name" => "required|string",
            "father_mobile" => "required|string|size:11",
            "amen_name" => "required|string",
            "amen_mobile" => "required|string|size:11",
            'logo' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'amen_national_image' => 'nullable|mimes:jpeg,png,jpg|max:4096',
        ]);
        $userData = $request->only(["name","mobile","email","status"]);
        if ($request->has("password") && !is_null($request->password)){
            $userData["password"] = Hash::make($request->password);
        }
        try{
            DB::beginTransaction();
            $user = User::query()->whereId($id)->first();
            $user->update($userData);
            if ($request->has("logo") && !is_null($request->logo)){
                deleteOldFiles("uploads/job_office/" . $user->id . "/logo");
                $user->update(["image" => uploadImage($request->logo, "uploads/job_office/" . $user->id . "/logo")]);
            }
            $jobOfficeData = $request->only(["father_name", "church_name", "father_mobile", "amen_name", "amen_mobile"]);
            $company = JobOfficeDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $jobOfficeData);
            if ($request->amen_national_image) {
                deleteOldFiles("uploads/job_office/" . $user->id . "/amen_national_image");
                $company->update(["amen_national_image" => uploadImage($request->amen_national_image, "uploads/job_office/" . $user->id . "/amen_national_image")]);
            }
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
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.job_offices.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }




    }//end of update

    public function updateStatus(Request $request){
        $request->validate([
           "user_id" => ["required",Rule::exists("users","id")->where("role","job_office")],
           "status" => "required|in:active,pending,deleted,inProgress",
        ]);
        $userCompany = User::find($request->user_id);
        $userCompany->update(["status" => $request->status]);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.job_offices.index');

    }



    public  function destroy($id)
    {
        $userCompany = User::find($id);
        $userCompany->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.job_offices.index');

    }//end of destroy

    public function export(){
        return Excel::download(new CompanyExport(), 'job_offices.csv', \Maatwebsite\Excel\Excel::CSV);
    }

}//end of controller
