<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = User::company()->latest()->paginate(20);
        return view('dashboard.companies.index', compact('companies'));
    }//end of index

    public
    function create()
    {
        return view('dashboard.companies.create');

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            "administrator_name" => "required|string",
            "administrator_mobile" => "required|string|size:11",
            'bio' => 'required|string',
            'created_date' => 'required|date',
            'address' => 'required|string',
            'logo' => 'required|mimes:jpeg,png,jpg|max:2048',
            'commercial_record_image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'tax_card_image' => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);
        $userData = $request->only(["name","mobile","email"]);
        $userData["password"] = Hash::make($request->password);
        try{
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ],[
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "role" =>  "company",
                "status" => "active",
            ]);
            deleteOldFiles("uploads/companies/".$user->id."/logo");
            if ($request->logo) {
                $user->update(["image" => uploadImage($request->logo,"uploads/companies/".$user->id."/logo/".generateBcryptHash($user->id)."/logo")]);
            }
            $user->attachRole('company');
            $companyData = $request->only(["administrator_name","administrator_mobile","bio","address"]);
            $companyData["created_date"] = Carbon::parse($request->created_date)->toDateString();
            $company = CompanyDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ],$companyData);
            deleteOldFiles("uploads/companies/".$user->id."/commercial_record");
            if ($request->commercial_record_image) {
                $company->update(["commercial_record_image" => uploadImage($request->commercial_record_image,"uploads/companies/".$user->id."/commercial_record/".generateBcryptHash($user->id)."/commercial_record")]);
            }
            deleteOldFiles("uploads/companies/".$user->id."/tax_card");
            if ($request->tax_card_image) {
                $company->update(["tax_card_image" => uploadImage($request->tax_card_image,"uploads/companies/".$user->id."/tax_card/".generateBcryptHash($user->id)."/tax_card")]);
            }
            DB::commit();
            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.companies.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of store

    public function edit($id)
    {
        $jobCompany = User::Company()->whereId($id)->with("company_details")->first();
        if (is_null($jobCompany)){
            return  abort(404);
        }
        return view('dashboard.companies.edit', compact('jobCompany'));
    }//end of user

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'status' => 'required|string',
            'mobile' => ["required","string",'size:11',Rule::unique('users', 'mobile')->ignore($request->user_id)],
            'email' => ['required',"email",Rule::unique('users', 'email')->ignore($request->user_id)],
            "administrator_name" => "required|string",
            "administrator_mobile" => "required|string|size:11",
            'bio' => 'required|string',
            'created_date' => 'required|date',
            'address' => 'required|string',
            'logo' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'commercial_record_image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
            'tax_card_image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
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
                deleteOldFiles("uploads/companies/".$user->id."/logo");
                $user->update(["image" => uploadImage($request->logo,"uploads/companies/".$user->id."/logo/".generateBcryptHash($user->id)."/logo")]);
            }
            $companyData = $request->only(["administrator_name","administrator_mobile","bio","address"]);
            $companyData["created_date"] = Carbon::parse($request->created_date)->toDateString();
            $company = CompanyDetail::whereUserId($id)->first();
            $company->update($companyData);
            if ($request->has("commercial_record_image") && !is_null($request->commercial_record_image)){
                deleteOldFiles("uploads/companies/".$user->id."/commercial_record");
                $company->update(["commercial_record_image" => uploadImage($request->commercial_record_image,"uploads/companies/".$user->id."/commercial_record/".generateBcryptHash($user->id)."/commercial_record")]);
            }
            if ($request->has("tax_card_image") && !is_null($request->tax_card_image)) {
                deleteOldFiles("uploads/companies/".$user->id."/tax_card");
                $company->update(["tax_card_image" => uploadImage($request->tax_card_image,"uploads/companies/".$user->id."/tax_card/".generateBcryptHash($user->id)."/tax_card")]);
            }
            if ($request->has("notify") && !is_null($request->notify)) {
                $recipients = [$user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),$request->notify,"posts");
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.companies.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }




    }//end of update

    public function updateStatus(Request $request){
        $request->validate([
           "user_id" => ["required",Rule::exists("users","id")->where("role","company")],
           "status" => "required|in:active,pending,deleted,inProgress",
        ]);
        $userCompany = User::find($request->user_id);
        $userCompany->update(["status" => $request->status]);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.companies.index');

    }



    public  function destroy($id)
    {
        $userCompany = User::find($id);
        $userCompany->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.companies.index');

    }//end of destroy



}//end of controller
