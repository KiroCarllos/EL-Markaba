<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JobController extends Controller
{

    public function index(Request $request)
    {
        $jobs = Job::latest()->paginate(20);

        return view('dashboard.jobs.index', compact('jobs'));

    }//end of index

    public
    function create()
    {
        $companies = Company::all();
        return view('dashboard.jobs.create',compact('companies'));

    }//end of create

    public
    function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'contact_email' => 'required|email',
            'company_id' => ['required','numeric',Rule::exists("companies","id")],
            'address' => 'required',
            'location' => 'nullable',
            'salary' => 'required',
        ]);
        $request_data = $request->only(['title','company_id', 'description', 'type', 'contact_email', 'address', 'location', 'salary']);
        $job = Job::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of store

    public
    function edit(Job $job)
    {
        $companies = JobCompany::all();
        return view('dashboard.jobs.edit', compact('job','companies'));

    }//end of user

    public
    function update(Request $request, Job $job)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required',
            'contact_email' => 'required|email',
            'company_id' => ['required',Rule::exists('companies',"id")],
            'address' => 'required',
            'location' => 'nullable',
            'salary' => 'required',
        ]);
        $request_data = $request->only(['title','company_id', 'description', 'type', 'contact_email', 'address', 'location', 'salary']);
        $job->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of update

    public
    function destroy(Job $job)
    {

        $job->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of destroy

}//end of controller
