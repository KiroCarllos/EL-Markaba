<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Job;
use App\Models\JobApplication;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    public function getAvailJobs(){
        $jobs = Job::where("status","active")->with("company")->latest()->paginate(15);
        return api_response(1,"",$jobs);
    }
    public function getJobDetails(Request $request){
        $request->validate([
            "job_id" => ["required","numeric",Rule::exists("jobs","id")]
        ]);
        $job = Job::whereId($request->job_id)->with("company")->first();
        $myJob_ids = JobApplication::where("job_id",$job->id)->pluck("user_id")->toArray();
        // remove status
        $status = in_array(auth("api")->id(),$myJob_ids) ? JobApplication::where("job_id",$job->id)->pluck("status")->first(): null;
        $job->setAttribute("application_status",$status);
        return api_response(1,"",$job);
    }
}//end of controller
