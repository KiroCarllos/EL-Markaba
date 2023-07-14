<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobController extends Controller
{

    public function index(Request $request)
    {

        $jobs = Job::Active()->whereHas("company")->latest()->paginate(20);
//        dd($jobs);
        return view('dashboard.jobs.index', compact('jobs'));

    }//end of index

    public
    function create()
    {
        $job_companies =  User::company()->latest()->get();
        return view('dashboard.jobs.create',compact('job_companies'));

    }//end of create

    public
    function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'work_type' => 'required|in:part_time,full_time',
            'work_hours' => 'nullable',
            'contact_email' => 'required|email',
            'user_id' => ['required','numeric',Rule::exists("users","id")->where("role","company")],
            'address' => 'required',
            'location' => 'nullable',
            'expected_salary_from' => 'required|numeric',
            'expected_salary_to' => 'required|numeric',
        ]);
        $request_data = $request->only(['title_ar','title_en','user_id', 'description_ar','description_en', 'work_type',"work_hours", 'contact_email', 'address', 'location', 'expected_salary_from','expected_salary_to']);
        $job = Job::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of store

    public
    function edit($id)
    {
        $job = Job::findOrFail($id);
        $companies =  User::company()->latest()->get();
        return view('dashboard.jobs.edit', compact('job','companies'));
    }//end of user

    public
    function update(Request $request, $id)
    {
        $request->validate([
            'title_ar' => 'required',
            'title_en' => 'required',
            'description_ar' => 'required',
            'description_en' => 'required',
            'work_type' => 'required|in:part_time,full_time',
            'work_hours' => 'nullable',
            'contact_email' => 'required|email',
            'user_id' => ['required','numeric',Rule::exists("users","id")->where("role","company")],
            'address' => 'required',
            'location' => 'nullable',
            'expected_salary_from' => 'required|numeric',
            'expected_salary_to' => 'required|numeric',
        ]);
        $job = Job::findOrFail($id);

        $request_data = $request->only(['title_ar','title_en','user_id','status', 'description_en','description_ar', 'work_type',"work_hours", 'contact_email', 'address', 'location', 'expected_salary_from','expected_salary_to']);
        $job->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of update

    public
    function destroy($id)
    {
        $job = Job::find($id);
        $job->status ="deleted";
        $job->save();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of destroy

}//end of controller
