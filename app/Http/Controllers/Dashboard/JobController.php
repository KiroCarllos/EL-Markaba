<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobController extends Controller
{

    public function index(Request $request)
    {

        $jobs = Job::whereNotIn("status",["deleted"])->whereHas("company")->latest()->paginate(20);
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
            'job_type' => 'required|in:from_company,online',
            'work_hours' => 'nullable',
            'contact_email' => 'required|email',
            'user_id' => ['required','numeric',Rule::exists("users","id")->where("role","company")],
            'address' => 'required',
            'location' => 'nullable',
            'expected_salary_from' => 'required|numeric',
            'expected_salary_to' => 'required|numeric',
        ]);
        $request_data = $request->only(['title_ar','title_en','job_type','user_id', 'description_ar','description_en', 'work_type',"work_hours", 'contact_email', 'address', 'location', 'expected_salary_from','expected_salary_to']);
        $request_data["status"] = "pending";
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
            'job_type' => 'required|in:from_company,online',
            'work_hours' => 'nullable',
            'contact_email' => 'required|email',
            'user_id' => ['required','numeric',Rule::exists("users","id")->where("role","company")],
            'address' => 'required',
            'location' => 'nullable',
            'expected_salary_from' => 'required|numeric',
            'expected_salary_to' => 'required|numeric',
        ]);
        $job = Job::findOrFail($id);

        $request_data = $request->only(['title_ar','job_type','title_en','user_id','status', 'description_en','description_ar', 'work_type',"work_hours", 'contact_email', 'address', 'location', 'expected_salary_from','expected_salary_to']);
        if (($job->status == "pending"  && $request->status == "active") ||($job->status == "pending" && $request->status == "active") ){
//            $recipients = [$application->user->device_token];
            $recipients = ["ehOW5v2HQe24R-gXOtAYe1:APA91bGsa09PkCmPjIsXCb2TXEeL754sELEvDK70jP8U5P-8i3QyWaT1nFNL_dzjgL8KTvd3HuuctgfrtPH1I2Ul3kpwWqLFupuUCkL-qYT2iDbKMx-_B6YmPudOckDniV3qGCX1LS-L","emRGezSxRQSLazd0NESfzi:APA91bHagdojOO_T5eGZXkwzzN4kQpPnrmM7_K78UwKGl9LmZKggoKBbAAeb3BRKmBfe513tjN3eZuzxmoK48Md4wcy-NFbkFiHR6bivi9W7e7s8CwC872lW03LHDFqLrt510nRTjj_t"];
            foreach ($recipients as $recipient){
                Notification::create([
                    "type" => "newJob",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.new_job_added"),
                    "read" => "0",
                    "model_id" => $job->id,
                    "model_json" => $job,
                    "user_id" => $job->id,
                ]);
                send_fcm([$recipient],__("site.markz_el_markaba"),__("site.new_job_added"),"jobs",$job);
            }
        }
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






    public function applications($id){
        $applications = JobApplication::where("job_id",$id)->where("status","!=","canceled")->whereHas("user")->get();
        return view('dashboard.jobs.applications.index', compact('applications'));
    }
    public  function editApplication($id)
    {
        $application = JobApplication::findOrFail($id);
        $userStudentDetail = User::whereId($application->user_id)->with("student_details")->first();
        if ($userStudentDetail->student_details->education == "high"){
            if (is_null($userStudentDetail->student_details->faculty)){
                $faculties=[];
            }else{
                $faculties = $this->getFacultyByUniversityById($userStudentDetail->student_details->faculty->university_id);
            }
        }else{
            $faculties=[];
        }
        $universities = $this->getAllUniversities();
        return view('dashboard.jobs.applications.edit', compact('application','faculties','universities'));
    }//end of destroy

    public function updateApplication(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'notify' => 'nullable',
        ]);
        $jobData = $request->only(["status"]);
        try{
            DB::beginTransaction();
            $jobApplication = JobApplication::query()->whereId($id)->first();
            $job = Job::query()->whereId($jobApplication->job_id)->first();
//            if ($jobApplication->status == "confirmed" && $request->status == "confirmed"){
//
//            }else if ($jobApplication->status != "pending" && $request->status == "confirmed"){
//                $recipients = [$jobApplication->user->device_token];
//                Notification::create([
//                    "type" => "myJob",
//                    "title" => __("site.markz_el_markaba"),
//                    "body" => __("site.your_job_has_been_confirmed"),
//                    "read" => "0",
//                    "model_id" => $job->id,
//                    "model_json" => $job,
//                    "user_id" => $jobApplication->user->id,
//                ]);
//                send_fcm($recipients,__("site.markz_el_markaba"),__("site.your_job_has_been_confirmed"),"myJob",$job);
//            } else  if ($jobApplication->status != "notConfirmed" && $request->status == "notConfirmed"){
//                $recipients = [$jobApplication->user->device_token];
//                Notification::create([
//                    "type" => "myJob",
//                    "title" => __("site.markz_el_markaba"),
//                    "body" => __("site.sorry_your_job_application_have_some_notes"),
//                    "read" => "0",
//                    "model_id" => $job->id,
//                    "model_json" => $job,
//                    "user_id" => $jobApplication->user->id,
//                ]);
//                send_fcm($recipients,__("site.markz_el_markaba"),__("site.sorry_your_job_application_have_some_notes"),"myJob",$job);
//            }

            if ($jobApplication->status == "pending" && $request->status == "inProgress"){
                $recipients = [$jobApplication->user->device_token,$job->company->device_token];
                foreach ($recipients as $index=>$recipient){
                    dd($index);
                }
                Notification::create([
                        "type" => "myJob",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.you_application_under_review_from_company"),
                    "read" => "0",
                    "model_id" => $job->id,
                    "model_json" => $job,
                    "user_id" => $jobApplication->user->id,
                ]);
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_application_under_review_from_company"),"myJob",$job);
                // comapny
                Notification::create([
                    "type" => "jobs",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.student_has_suggest_for_job"),
                    "read" => "0",
                    "model_id" => $job->id,
                    "model_json" => $job,
                    "user_id" => $job->company->id,
                ]);
                dd([]);
                send_fcm([$job->company->device_token],__("site.markz_el_markaba"),__("site.student_has_suggest_for_job"),"jobs",$job);


                $jobApplication->update($jobData);
            }elseif ($jobApplication->status == "inProgress" && $request->status == "pending"){
                $jobApplication->update($jobData);
            }elseif (($jobApplication->status == "inProgress" && $request->status == "canceled") || $jobApplication->status == "pending" && $request->status == "canceled"){
                $recipients = [$jobApplication->user->device_token];
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.we_really_sorry_your_application_has_been_rejected"),
                    "read" => "0",
                    "model_id" => $job->id,
                    "model_json" => $job,
                    "user_id" => $jobApplication->user->id,
                ]);
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.we_really_sorry_your_application_has_been_rejected"),"posts",$job);
                $jobApplication->update($jobData);
            }else if ($request->has("notify") && !is_null($request->notify)) {
                $recipients = [$jobApplication->user->device_token];
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => $request->notify,
                    "read" => "0",
                    "model_id" => $job->id,
                    "model_json" => $job,
                    "user_id" => $jobApplication->user->id,
                ]);
                send_fcm($recipients,__("site.markz_el_markaba"),$request->notify,"posts",$job);
            }


            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.jobs.applications',$jobApplication->job_id);
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of update
    public  function deleteApplication($id)
    {
        $job = JobApplication::findOrFail($id);
        $job->update(["status" => "canceled"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.jobs.applications',$job->job_id);
    }//end of destroy

}//end of controller
