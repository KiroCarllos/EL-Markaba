<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Training;
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
        try{
            DB::beginTransaction();
            $training = Training::query()->firstOrCreate($trainingData);
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


}//end of controller
