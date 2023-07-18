<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Slider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SliderController extends Controller
{
    public function index(){
        $sliders = Slider::paginate(10);
        return view('dashboard.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('dashboard.sliders.create');

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'role' => ['required',"array","min:1","in:company,student"],
            'status' => 'required|in:active,disActive',
            'image' => 'required',
        ]);
        $sliderData = $request->only(["role","status"]);
        try{
            DB::beginTransaction();
            $slider = Slider::query()->create($sliderData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/sliders/".$slider->id."/image");
                $slider->update(["image" => uploadImage($request->image,"uploads/sliders/".$slider->id."/image/".generateBcryptHash($slider->id)."/image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.sliders.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of store

    public function edit($id)
    {
        $slider = Slider::findOrFail($id);
        return view('dashboard.sliders.edit', compact('slider'));
    }//end of user
    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => ['required',"array","min:1","in:company,student"],
            'status' => 'required|in:active,disActive,deleted',
            'image' => 'nullable',
        ]);
        $sliderData = $request->only(["role","status"]);
        try{
            DB::beginTransaction();
            $slider = Slider::query()->whereId($id)->first();
            $slider->update($sliderData);
            if ($request->has("image") && !is_null($request->image)){
                deleteOldFiles("uploads/sliders/".$slider->id."/image");
                $slider->update(["image" => uploadImage($request->image,"uploads/sliders/".$slider->id."/image/".generateBcryptHash($slider->id)."/image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.sliders.index');
        }catch (\Exception $exception){
            DB::rollBack();
            dd($exception);
        }
    }//end of update
    public  function destroy($id)
    {
        $slider = Slider::find($id);
        $slider->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.sliders.index');
    }//end of destroy


}//end of controller
