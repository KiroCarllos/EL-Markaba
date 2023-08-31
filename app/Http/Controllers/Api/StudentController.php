<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\Faculty;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostReply;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\User;
use App\Models\StudentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    // student login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_token' => 'nullable',
        ]);
        $user = User::where("email",$request->email)->first();
        if (!is_null($user)){
            if (Hash::check($request->password,$user->password)){
                if (!$token = auth("api")->login($user)) {
                    return api_response(0, __("api.These credentials do not match our records."));
                }
                if ($user->role == "student" || $user->role == "super_admin") {
                    if ($user->status == "active") {
                        $user->update(["auth_token" => $token,"device_token"=>$request->device_token]);
                        return api_response(1, __("site.student successfully login"), $user);
                    } else {
                        $msg = "Sorry Your Account is " . $user->status . " now";
                        return api_response(0, __("site.".$msg));
                    }

                } else {
                    return api_response(0, __("site.Sorry Your Account Not Be Student"), "");
                }
            }
            return api_response(0, __("site.These credentials do not match our records."), "");
        }
        return api_response(0, __("site.These credentials do not match our records."), "");




    }

    // student register
    public function register(Request $request)
    {
        $request->validate([
            'name' => ["required", "string","max:191"],
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|max:191|email|unique:users',
            'password' => 'required|max:191|confirmed',
            'image' => 'required|mimes:jpeg,png,jpg|max:4096',
            'gender' => 'required|in:male,female',
            'education' => 'required|in:high,medium,low,else',
            'national_id' => 'required|string|size:14',
            "prior_experiences" => ["nullable", "array"],
            "courses" => ["nullable", "array"],
            "device_token" => ["nullable","string"],
            "address" => ["required", "string"],
        ]);
        $userData = $request->only(["name", "mobile", "email","device_token"]);
        $userData["password"] = Hash::make($request->password);
        try {
            DB::beginTransaction();
            $user = User::query()->firstOrCreate([
                "mobile" => $userData["mobile"]
            ], [
                "name" => $userData["name"],
                "email" => $userData["email"],
                "password" => $userData["password"],
                "device_token" => $userData["device_token"],
                "role" => "student",
            ]);
            deleteOldFiles("uploads/student/" . $user->id . "/profile");
            if ($request->image) {
                $user->update(["image" => uploadImage($request->image, "uploads/student/" . $user->id . "/profile")]);
            }
            $user->attachRole('student');
            if ($request->education == "else"){
                $request->validate([
                    "else_education" => ["required","string","max:191"]
                ]);
            }else{
                $request->validate([
                    'major' => ["required", "string","max:191"],
                    'faculty_id' => ["required", "numeric",Rule::exists("faculties","id")],
                    'graduated_at' => ['required', 'date_format:Y'],
                ]);
            }
            $studentData = $request->only(["gender","education","else_education", "national_id", "faculty_id","major", "graduated_at", "prior_experiences", "courses", "address"]);
            $studentData = StudentDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $studentData);

            if ($request->has("device_token")&& !is_null($request->device_token)){
                $recipients = $request->only($request->device_token);
                Notification::create([
                    "type" => "newAccount",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.your_account_added_please_wait_activation"),
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                ]);
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.your_account_added_please_wait_activation"),"newAccount",$user);
            }
            DB::commit();
            return api_response(1, __("site.student created successfully wait admins for approve"));
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception);
        }
    }

    // student profile
    public function profile()
    {
        $user = User::query()->where("id", auth("api")->id())->with("student_details")->first();
        return api_response(1, __("site.profile student get successfully"), $user);
    }

    // student logout
    public function logout()
    {
        $user = auth("api")->user();
        $user->update(["auth_token" => null]);
        return api_response(1, __("site.student signOut successfully"));
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
            return api_response(1, __("site.student deleted successfully"));
        }
        return api_response(0, __("site.Sorry Wrong Password"));
    }
    public function getPosts(){
        $posts = Post::active()->withCount("replies")->latest()->paginate(6);
        return api_response(1,"",$posts);
    }
    public function replyPost(Request $request){
        $request->validate([
            "post_id" => ["required",Rule::exists("posts","id")->where("status","active")],
            "reply" => ["required","string"],
        ]);
        $postReply = PostReply::query()->firstOrCreate([
            "post_id" => $request->post_id,
            "user_id" => auth("api")->id(),
            "reply" => $request->reply,
        ]);
        return api_response(1,__("site.Replied Successfully"),"");
    }

    public function getTrainings(){
        $trainings = Training::active()->withCount("applications")->latest()->paginate(6);
//        foreach ($trainings as $training){
//            $mytraining_ids = TrainingApplication::where("training_id",$training->id)->pluck("user_id")->toArray();
//            // remove status
//            if(in_array(auth("api")->id(),$mytraining_ids)){
//                $status =TrainingApplication::where("training_id",$training->id)->pluck("status")->first();
//                $training->setAttribute("application_status",$status);
//                $training->setAttribute("applied",true);
//            }else{
//                $training->setAttribute("application_status",null);
//                $training->setAttribute("applied",false);
//            }
//        }
        return api_response(1,"",$trainings);
    }

    public function applyTraining(Request $request){
        $request->validate([
            "training_id" => ["required",Rule::exists("trainings","id")->where("status","active")],
        ]);
        $training = Training::find($request->training_id);
        if ($training->status == "enough"){
            return api_response(0,"Sorry this Job Enough You can choose anther");
        }
        $applyTraining = TrainingApplication::query()->firstOrCreate([
            "training_id" => $request->training_id,
            "user_id" => auth("api")->id(),
        ]);
        if ($training->paid == "no"){
             $applyTraining->update(["status" => "inProgress"]);
        }
        if ($applyTraining->status == "canceled"){
             $applyTraining->update(["status" => "pending"]);
        }
        if (auth("api")->user()->device_token&& !is_null(auth("api")->user()->device_token)){
            $recipients = [auth("api")->user()->device_token];
            Notification::create([
                "type" => "pendingTraining",
                "title" => __("site.markz_el_markaba"),
                "body" => __("site.you_has_apply_training_and_now_pending"),
                "read" => "0",
                "model_id" => $training->id,
                "model_json" => $training,
                "user_id" => auth("api")->id(),
            ]);
            send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_apply_training_and_now_pending"),"pendingTraining",$training);
        }
        return api_response(1,__("site.Applied Training Successfully"),TrainingApplication::find($applyTraining->id));
    }
    public function myTrainings(){
        $mytraining_ids = TrainingApplication::IgnoreCancel()->where("user_id",auth("api")->id())->pluck("training_id")->toArray();
        $mytrainings = Training::ActiveMyTraining()->whereIn("id",$mytraining_ids)->latest()->get();
        foreach ($mytrainings as $training){
            $mytraining_ids = TrainingApplication::where("training_id",$training->id)->where("user_id",auth("api")->id())->pluck("status")->first();
            $training->setAttribute("application_status",$mytraining_ids);
        }
        return api_response(1,"",$mytrainings);
    }

    public function confirmAppliedTraining(Request $request){
        $request->validate([
            "training_id" => ["required",Rule::exists("trainings","id")],
            "receipt_image" => 'required|mimes:jpeg,png,jpg|max:4096',
        ]);
        $mytraining_ids = TrainingApplication::where("user_id",auth("api")->id())->pluck("training_id")->toArray();
        if (in_array($request->training_id,$mytraining_ids)){
            $training_application = TrainingApplication::whereUserId(auth("api")->id())->where("training_id",$request->training_id)->first();
            if ($training_application->status == "inProgress" && !is_null($training_application->receipt_image)){
                return api_response(1,__("site.Please Wait Admins Confirmation"));
            }else if ($training_application->status !== "confirmed"){
                $training = Training::find($request->training_id);
                if ($request->has("receipt_image") && is_file($request->receipt_image)){
                    deleteOldFiles("uploads/student/" . auth("api")->id() . "/training/".$request->training_id."/receipt_image");
                    if ($request->receipt_image) {
                        if (auth("api")->user()->device_token&& !is_null(auth("api")->user()->device_token)){
                            $recipients = [auth("api")->user()->device_token];
                            Notification::create([
                                "type" => "pendingTraining",
                                "title" => __("site.markz_el_markaba"),
                                "body" => __("site.you_has_apply_training_and_now_pending"),
                                "read" => "0",
                                "model_id" => $request->training_id,
                                "model_json" => $training,
                                "user_id" => auth("api")->id(),
                            ]);
                            send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_apply_training_and_now_pending"),"pendingTraining",$training);
                        }
                        $training_application->update(["status" => "inProgress","receipt_image" => uploadImage($request->receipt_image, "uploads/student/training/" . auth("api")->id() . "/".$request->training_id."/receipt_image")]);
                    }
                }
            }

            return api_response(1,__("site.Your Application Applied Please Wait Admins Confirmation"));
        }else{
            return api_response(1,__("site.sorry this training you haven't applied before"));

        }
    }
    public function cancelAppliedTraining(Request $request){
        $request->validate([
            "training_id" => ["required",Rule::exists("trainings","id")->where("status","active")],
        ]);
        $applyTraining = TrainingApplication::where("training_id",$request->training_id)->where("user_id",auth("api")->id())->first();
        if (!is_null($applyTraining)){

            if (auth("api")->user()->device_token&& !is_null(auth("api")->user()->device_token)){
                $recipients = [auth("api")->user()->device_token];
                Notification::create([
                    "type" => "myTraining",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.you_has_delete_training_successfully"),
                    "read" => "0",
                    "model_id" => $request->training_id,
                    "model_json" => Training::find($request->training_id),
                    "user_id" => auth("api")->id(),
                ]);
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_delete_training_successfully"),"myTraining",Training::find($request->training_id));
            }
            $applyTraining->delete();
            return api_response(1,__("site.Training canceled successfully"));
        }else{
            return api_response(0,__("site.Invalid Training id"));
        }
    }

    public function applyJob(Request $request){
        $request->validate([
            "job_id" => ["required",Rule::exists("jobs","id")],
        ]);
        $job = Job::find($request->job_id);
        $jobApplication = JobApplication::where("job_id",$request->job_id)->where("user_id",auth("api")->id())->first();
        if ($job->status == "enough"){
            return api_response(0,__("site.Sorry this Job Enough You can choose anther"));
        }
        if (!is_null($jobApplication)){
            if ($jobApplication->status == "canceled"){
                $jobApplication->update(["status" => "pending"]);
            }
        }
        if ($job->status == "active"){
            $applyJob = JobApplication::query()->firstOrCreate([
                "job_id" => $request->job_id,
                "user_id" => auth("api")->id(),
            ]);
            return api_response(1,__("site.Applied Job Successfully"));
        }else{
            $msg = "Sorry This Job ".$job->status." Please try later";
            return api_response(0,__("site.".$msg));
        }

    }
    public function myJobs(){
//        $mytrainings = TrainingApplication::where("user_id",auth("api")->id())->with("training")->get();
        $myJob_ids = JobApplication::IgnoreCancel()->where("user_id",auth("api")->id())->pluck("job_id")->toArray();
        $myJobs = Job::whereIn("id",$myJob_ids)->with("company")->latest()->get();
        foreach ($myJobs as $job){
            $myJob_ids = JobApplication::where("job_id",$job->id)->where("user_id",auth("api")->id())->pluck("status")->first();
            $job->setAttribute("application_status",$myJob_ids);
        }
        return api_response(1,"",$myJobs);
    }
    public function cancelAppliedJob(Request $request){
        $request->validate([
            "job_id" => ["required",Rule::exists("jobs","id")],
        ]);
        $applyJob = JobApplication::where("job_id",$request->job_id)->where("user_id",auth("api")->id())->first();
        if (is_null($applyJob)){
            return api_response(0,__("site.Sorry inValid Job"));
        }else{
            $applyJob->delete();
            return api_response(1,__("site.Job Canceled Successfully"));
        }
    }
    public function getJobDetails(Request $request){
        $request->validate([
            "job_id" => ["required","numeric",Rule::exists("job_applications","job_id")->where("user_id",auth("api")->id())]
        ]);
        $job = Job::whereId($request->job_id)->with("company")->first();
        if (is_null($job) || $job->status == "deleted"){
            return api_response(0,"sorry inValid Job ID","");
        }
        $myJob_ids = JobApplication::where("job_id",$job->id)->where("user_id",auth("api"))->first();
        $job->setAttribute("application_status",$myJob_ids->status);
        return api_response(1,"",$job);
    }


    public function notifications(){
        $notifications = Notification::where("user_id",auth("api")->id())->latest()->paginate(10);
        return api_response(1,"",$notifications);
    }

    public function updateNotification(Request $request){
        $request->validate([
            "notification_id" => ["required","numeric",Rule::exists("notifications","notification_id")->where("user_id",auth("api")->id())],
            "read" => ["required","in:0,1"]
        ]);
        $notification = Notification::find($request->notification_id);
        $notification->update(["read" => $request->read]);
        return api_response(1,__('site.updated_successfully'));
    }




}//end of controller
