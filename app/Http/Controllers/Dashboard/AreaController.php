<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\JobApplicationExport;
use App\Exports\JobExport;
use App\Http\Controllers\Controller;
use App\Jobs\AddNewJob;
use App\Models\Area;
use App\Models\ChatMessage;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AreaController extends Controller
{

    public function index(Request $request)
    {

        $areas = Area::latest()->get();
//        dd($jobs);
        return view('dashboard.areas.index', compact('areas'));

    }//end of index

    public
    function create()
    {
        return view('dashboard.jobs.create');

    }//end of create

    public
    function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
        ]);
        $request_data = $request->only(['name_ar','name_en']);
        $area = Area::create($request_data);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.areas.index');

    }//end of store

    public
    function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('dashboard.areas.edit', compact('area'));
    }//end of user

    public
    function update(Request $request, $id)
    {
        $request->validate([
            'name_ar' => 'required',
            'name_en' => 'required',
            'area_id' => ['required','numeric',Rule::exists("areas","id")],
        ]);
        $area = Area::findOrFail($request->area_id);

        $request_data = $request->only(['name_ar','name_en']);

        $area->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.areas.index');

    }//end of update

    public
    function destroy($id)
    {
        $area = Area::find($id);
        $area->save();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.jobs.index');

    }//end of destroy





}//end of controller
