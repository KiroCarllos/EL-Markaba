<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Training;
use App\Models\TrainingApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TrainingController extends Controller
{

    public function index(){
        $trainings = Training::whereIn("status",["active","disActive"])->withCount("applications")->paginate(10);
        return view('dashboard.trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('dashboard.trainings.create');

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'status' => 'required|in:active,disActive',
            'paid' => 'required|in:yes,no',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        $trainingData = $request->only(["title_en","paid","title_ar","description_en","description_ar","status"]);
        $trainingData["created_at"] =  Carbon::now()->timezone('Africa/Cairo')->toDateTimeString();
        $trainingData["updated_at"] = Carbon::now()->timezone('Africa/Cairo')->toDateTimeString();
        $trainingData["user_id"] = auth()->id();
        try{
            DB::beginTransaction();
            $training = Training::query()->firstOrCreate($trainingData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/trainings/".$training->id."/image");
                $training->update(["image" => uploadImage($request->image,"uploads/trainings/".$training->id."/image/".generateBcryptHash($training->id)."/image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));

            $recipients = User::whereNotNull("device_token")->pluck("device_token")->toArray();
            send_fcm($recipients,__("site.markz_el_markaba"),__("site.you_has_add_training_successfully"),"trainings");

            return redirect()->route('dashboard.trainings.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of store

    public function edit($id)
    {
        $training = Training::findOrFail($id);
        return view('dashboard.trainings.edit', compact('training'));
    }//end of user
    public function update(Request $request, $id)
    {
        $request->validate([
            'title_en' => 'required|string',
            'title_ar' => 'required|string',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'paid' => 'required|in:yes,no',
            'status' => 'required|in:active,disActive',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        $trainingData = $request->only(["title_en","paid","title_ar","description_en","description_ar","status"]);

        try{
            DB::beginTransaction();
            $training = Training::query()->whereId($id)->first();
            $training->update($trainingData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/trainings/".$training->id."/image");
                $training->update(["image" => uploadImage($request->image,"uploads/trainings/".$training->id."/image/".generateBcryptHash($training->id)."/image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.trainings.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of update

    public  function destroy($id)
    {
        $training = Training::find($id);
        $training->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.trainings.index');
    }//end of destroy



    public function applications($id){
        $applications = TrainingApplication::where("training_id",$id)->where("status","!=","canceled")->whereHas("user")->get();
        return view('dashboard.trainings.applications.index', compact('applications'));
    }
    public  function editApplication($id)
    {
        $application = TrainingApplication::findOrFail($id);
        return view('dashboard.trainings.applications.edit', compact('application'));
    }//end of destroy

    public function updateApplication(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'notify' => 'nullable',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:2048',
        ]);
        $trainingData = $request->only(["status"]);
        try{
            DB::beginTransaction();
            $trainingApplication = TrainingApplication::query()->whereId($id)->first();
            if ($trainingApplication->status != "pending" && $request->status == "confirmed"){
                $recipients = [$trainingApplication->user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.your_training_has_been_confirmed"),"myTraining");
            }
            if ($trainingApplication->status != "inProgress" && $request->status == "confirmed"){
                $recipients = [$trainingApplication->user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.your_training_has_been_confirmed"),"myTraining");
            }
            if ($trainingApplication->status != "enough" && $request->status == "enough"){
                $recipients = [$trainingApplication->user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.sorry_your_training_has_been_enough_numbers"),"myTraining");
            }
            if ($trainingApplication->status != "notConfirmed" && $request->status == "notConfirmed"){
                $recipients = [$trainingApplication->user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),__("site.sorry_your_training_application_have_some_notes"),"myTraining");
            }
            if ($request->has("notify") && !is_null($request->notify)) {
                $recipients = [$trainingApplication->user->device_token];
                send_fcm($recipients,__("site.markz_el_markaba"),$request->notify,"posts");
            }
            $trainingApplication->update($trainingData);
            if ($request->has("receipt_image") && !is_null($request->receipt_image)){
                deleteOldFiles("uploads/trainings/application/".$id."/receipt_image");
                $trainingApplication->update(["receipt_image" => uploadImage($request->receipt_image,"uploads/trainings/application/".$id."/receipt_image/".generateBcryptHash($id)."/receipt_image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.trainings.applications',$trainingApplication->training_id);
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of update
    public  function deleteApplication($id)
    {
        $training = TrainingApplication::findOrFail($id);
        $training->update(["status" => "canceled"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.trainings.applications',$training->training_id);
    }//end of destroy


}//end of controller
