<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetail;
use App\Models\Faculty;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Major;
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
        ]);
        $credentials = ["email" => $request->email, "password" => $request->password];
        if (!$token = auth("api")->attempt($credentials)) {
            return api_response(0, __("site.These credentials do not match our records."), "");
        }
        $user = User::where("email", $request->email)->first();
        if ($user->role == "student" || $user->role == "super_admin") {
            if ($user->status == "active") {
                $user->update(["auth_token" => $token]);
                return api_response(1, __("site.student successfully login"), $user);
            } else {
                $msg = "Sorry Your Account is " . $user->status . " now";
                return api_response(0, __("site.".$msg));
            }

        } else {
            return api_response(0, __("site.Sorry Your Account Not Be Student"), "");
        }
    }

    // student register
    public function register(Request $request)
    {
        $request->validate([
            'name' => ["required", "string","max:191"],
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|max:191|email|unique:users',
            'password' => 'required|max:191|confirmed',
            'image' => 'required|mimes:jpeg,png,jpg|max:2048',
            'gender' => 'required|in:male,female',
            'education' => 'required|in:high,medium,low,else',
            'national_id' => 'required|string|size:14',
            "prior_experiences" => ["nullable", "array"],
            "courses" => ["nullable", "array"],
            "device_token" => ["nullable","string"],
            "address" => ["required", "string"],
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


            $recipients = ["dxWUemmZSkm7zQdmpxWrNJ:APA91bELXt2_xq-oZXJfepfzBgFtMtt_U_PbP94g_1O00myoi7yxLha3uXrXsSsI2BInC3bJ33n1QOPASDlALzqIStutDSGKfhdwQF6-etB1L3YXEryd7D-_Dmd3s83k0Pz0cG2avz3d","c1lsSlYgQDiAZVDTBwD2W2:APA91bHXFurrWA-iZIiyRO3xcRFoDsipBv1_St1ds7-k3agcelUzfL02wsCFJDlFfvSTWpiT_oiBMLmujQ8QQJZfKQWxaxhwVT_fvOdJzO56l2lTxmfZyGGAZgb2Llp8AW0mAVxruT8-"];
            send_fcm($recipients,__("site.markz_el_markaba"),__("site.your_account_added_please_wait_activation"),"newAccount");
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
        foreach ($trainings as $training){
            $mytraining_ids = TrainingApplication::where("training_id",$training->id)->pluck("user_id")->toArray();
            // remove status
            $status = in_array(auth("api")->id(),$mytraining_ids) ? TrainingApplication::where("training_id",$training->id)->pluck("status")->first(): null;
            $training->setAttribute("application_status",$status);
            // end remove
            $training->setAttribute("applied",in_array(auth("api")->id(),$mytraining_ids));
        }
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

        $recipients = ["dxWUemmZSkm7zQdmpxWrNJ:APA91bELXt2_xq-oZXJfepfzBgFtMtt_U_PbP94g_1O00myoi7yxLha3uXrXsSsI2BInC3bJ33n1QOPASDlALzqIStutDSGKfhdwQF6-etB1L3YXEryd7D-_Dmd3s83k0Pz0cG2avz3d","c1lsSlYgQDiAZVDTBwD2W2:APA91bHXFurrWA-iZIiyRO3xcRFoDsipBv1_St1ds7-k3agcelUzfL02wsCFJDlFfvSTWpiT_oiBMLmujQ8QQJZfKQWxaxhwVT_fvOdJzO56l2lTxmfZyGGAZgb2Llp8AW0mAVxruT8-"];
        send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_apply_training_and_now_pending"),"pendingTraining");

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
            "receipt_image" => 'required|mimes:jpeg,png,jpg|max:2048',
        ]);
        $mytraining_ids = TrainingApplication::where("user_id",auth("api")->id())->pluck("training_id")->toArray();
        if (in_array($request->training_id,$mytraining_ids)){
            $training_application = TrainingApplication::whereUserId(auth("api")->id())->where("training_id",$request->training_id)->first();
            if ($training_application->status == "inProgress" && !is_null($training_application->receipt_image)){
                return api_response(1,"Please Wait Admins Confirmation");
            }else if ($training_application->status !== "confirmed"){
                if ($request->has("receipt_image") && is_file($request->receipt_image)){
                    deleteOldFiles("uploads/student/" . auth("api")->id() . "/training/".$request->training_id."/receipt_image");
                    if ($request->receipt_image) {
                        $recipients = ["dxWUemmZSkm7zQdmpxWrNJ:APA91bELXt2_xq-oZXJfepfzBgFtMtt_U_PbP94g_1O00myoi7yxLha3uXrXsSsI2BInC3bJ33n1QOPASDlALzqIStutDSGKfhdwQF6-etB1L3YXEryd7D-_Dmd3s83k0Pz0cG2avz3d","c1lsSlYgQDiAZVDTBwD2W2:APA91bHXFurrWA-iZIiyRO3xcRFoDsipBv1_St1ds7-k3agcelUzfL02wsCFJDlFfvSTWpiT_oiBMLmujQ8QQJZfKQWxaxhwVT_fvOdJzO56l2lTxmfZyGGAZgb2Llp8AW0mAVxruT8-"];
                        send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_apply_training_and_now_pending"),"pendingTraining");

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
            $applyTraining->delete();
            $recipients = ["dxWUemmZSkm7zQdmpxWrNJ:APA91bELXt2_xq-oZXJfepfzBgFtMtt_U_PbP94g_1O00myoi7yxLha3uXrXsSsI2BInC3bJ33n1QOPASDlALzqIStutDSGKfhdwQF6-etB1L3YXEryd7D-_Dmd3s83k0Pz0cG2avz3d","c1lsSlYgQDiAZVDTBwD2W2:APA91bHXFurrWA-iZIiyRO3xcRFoDsipBv1_St1ds7-k3agcelUzfL02wsCFJDlFfvSTWpiT_oiBMLmujQ8QQJZfKQWxaxhwVT_fvOdJzO56l2lTxmfZyGGAZgb2Llp8AW0mAVxruT8-"];
            send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_delete_training_successfully"),"myTraining");
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
        $myJobs = Job::Active()->whereIn("id",$myJob_ids)->with("company")->latest()->get();
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
}//end of controller
