<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;

class UserStudentDetailController extends Controller
{
    public function index(Request $request)
    {
        $user_student_details = StudentDetail::latest()->paginate(20);
        return view('dashboard.user_student_details.index', compact('user_student_details'));
    }//end of index

    public
    function create()
    {
        return view('dashboard.user_student_details.create');

    }//end of create

    public
    function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required|unique:users',
            'email' => 'required|unique:users',
            'image' => 'nullable',
            'password' => 'required|confirmed',

            'faculty' => 'required',
            'university' => 'required',
            'gender' => 'required',
            'national_id' => 'required|digits:14',
            'graduated_at' => 'required|date',
            'address' => 'required',
        ]);

        $request_user_data = $request->only(['name','mobile', 'email']);
        $request_user_detail_data = $request->only(['faculty','university', 'gender','national_id','graduated_at', 'address']);

        $request_user_data['password'] = bcrypt($request->password);
        if ($request->image) {
            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));
            $request_user_data['image'] = $request->image->hashName();

        }//end of if

        $user = User::create($request_user_data);
        $request_user_detail_data["user_id"] =$user->id;
        $user_details = StudentDetail::create($request_user_detail_data);
        $user->attachRole('student');
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.user_student_details.index');

    }//end of store

    public
    function edit(StudentDetail $userStudentDetail)
    {
        return view('dashboard.user_student_details.edit', compact('userStudentDetail'));

    }//end of user

    public
    function update(Request $request, StudentDetail $userStudentDetail)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($userStudentDetail->user->id),],
            'image' => 'nullable',
            'faculty' => 'nullable',
            'university' => 'nullable',
            'gender' => 'nullable',
            'national_id' => 'nullable|digits:14',
            'graduated_at' => 'nullable|date',
            'address' => 'nullable',
        ]);

        $request_user_data = $request->only(['name','mobile', 'email']);
        $request_user_data['password'] = bcrypt($request->password);
        $request_user_detail_data = $request->only(['faculty','university', 'gender','national_id','graduated_at', 'address']);

        if ($request->image) {

            if ($userStudentDetail->user->image != 'default.png') {

                Storage::disk('public_uploads')->delete('/user_images/' . $userStudentDetail->user->image);

            }//end of inner if

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $request_user_data['image'] = $request->image->hashName();

        }//end of external if

        $userStudentDetail->user->update($request_user_data);
        $userStudentDetail->update($request_user_detail_data);


        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.user_student_details.index');

    }//end of update

    public
    function destroy(StudentDetail $userStudentDetail)
    {
        if ($userStudentDetail->user->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/user_images/' . $userStudentDetail->user->image);
        }//end of if

        $userStudentDetail->user->delete();
        $userStudentDetail->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.user_student_details.index');

    }//end of destroy

}//end of controller
