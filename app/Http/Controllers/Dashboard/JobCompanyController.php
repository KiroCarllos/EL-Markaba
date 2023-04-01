<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\JobCompany;
use App\Permission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class JobCompanyController extends Controller
{public function index(Request $request)
    {
        $job_companies = JobCompany::latest()->paginate(20);
        return view('dashboard.job_companies.index', compact('job_companies'));

    }//end of index

    public
    function create()
    {
        return view('dashboard.job_companies.create');

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'bio' => 'nullable|string',
            'code' => 'nullable|string',
            'fax' => 'nullable|string',
            'commercial_record' => 'nullable|string',
            'tax_card' => 'nullable|string',
            'image' => 'nullable',
            'address' => 'nullable|string',
            'created_date' => 'nullable|date',
        ]);

        $request_data = $request->only([  'name', 'mobile', 'email']);
        $request_data['password'] = bcrypt($request->password);
        $request_data['role'] = "job_company";
        if ($request->image) {
            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/job_company_images/' . $request->image->hashName()));
            $request_data['image'] = $request->image->hashName();
        }//end of if
        $user = User::create($request_data);
        $user->attachRole('job_company');
        $job_company = JobCompany::create([
            "user_id" => $user->id,
            "name" => $request->name,
            "bio" => $request->bio,
            "fax" => $request->fax,
            "tax_card" => $request->tax_card,
            "created_date" => $request->created_date,
            "code" => $request->code,
            "commercial_record" => $request->commercial_record,
            "address" => $request->address,
        ]);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.job_companies.index');

    }//end of store

    public
    function edit(JobCompany $jobCompany)
    {
        return view('dashboard.job_companies.edit', compact('jobCompany'));

    }//end of user

    public
    function update(Request $request, JobCompany $jobCompany)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => ['required', Rule::unique('users')->ignore($jobCompany->user_id),],
            'email' => ['required', Rule::unique('users')->ignore($jobCompany->user_id),],
            'bio' => 'nullable|string',
            'code' => 'nullable|string',
            'fax' => 'nullable|string',
            'commercial_record' => 'nullable|string',
            'tax_card' => 'nullable|string',
            'image' => 'nullable',
            'address' => 'nullable|string',
            'created_date' => 'nullable|date',
        ]);
        $request_user_data = $request->only(['name', 'mobile','email']);
        $request_user_data['password'] = bcrypt($request->password);
        $request_company_data = $request->only(['bio', 'code','fax', 'commercial_record', 'tax_card','address', 'created_date']);

        if ($request->image) {
            if ($jobCompany->user->image != 'default.png') {
                Storage::disk('public_uploads')->delete('/job_company_images/' . $jobCompany->user->image);
            }//end of inner if

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/job_company_images/' . $request->image->hashName()));

            $request_user_data['image'] = $request->image->hashName();

        }//end of external if

        $jobCompany->user->update($request_user_data);
        $jobCompany->update($request_company_data);


        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.job_companies.index');

    }//end of update

    public
    function destroy(JobCompany $jobCompany)
    {
        if ($jobCompany->user->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/job_company_images/' . $jobCompany->user->image);
        }//end of if

        $jobCompany->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.job_companies.index');

    }//end of destroy

}//end of controller
