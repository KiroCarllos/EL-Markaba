<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function checkExpiredJobs(){

        $jobs = Job::whereDate('updated_at', '<', Carbon::now()->subDays(14))->get();
        foreach ($jobs as $job){
            $job->update(["status"=>"enough"]);
        }
       return api_response(1,"",$jobs);
    }
}//end of controller
