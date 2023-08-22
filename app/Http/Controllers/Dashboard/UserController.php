<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        //create read update delete
        $this->middleware(['role:super_admin'])->only('index');
        $this->middleware(['role:super_admin'])->only('create');
        $this->middleware(['role:super_admin'])->only('edit');
        $this->middleware(['role:super_admin'])->only('destroy');

    }//end of constructor

    public function index(Request $request)
    {
        $users = User::whereRoleIs('admin')->where(function ($q) use ($request) {

            return $q->when($request->search, function ($query) use ($request) {

                return $query->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%');

            });

        })->latest()->paginate(5);

        return view('dashboard.users.index', compact('users'));

    }//end of index

    public
    function create()
    {
        $permissions = Permission::query()->select("display_name")->get();
        foreach ($permissions as $permission){
            $models[] =explode(" ",$permission->display_name)[1];
        }
        $models = array_unique($models);
        foreach ($models as $i=>$model){
            $actions = Permission::query()->select("name")->where("display_name",'like', '%' . $model . '%')->get();
            foreach ($actions as $j=>$action){
                $maps[$model][$j] = explode("_",$action->name)[0];
            }
        }

        return view('dashboard.users.create',compact("models","maps"));

    }//end of create

    public
    function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'image' => 'required|File',
            'password' => 'required|confirmed',
            'permissions' => 'required|min:1'
        ]);

        $request_data = $request->except(['name','password', 'password_confirmation', 'permissions', 'image']);
        $request_data['password'] = bcrypt($request->password);
        if ($request->image) {
            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));
            $request_data['image'] = $request->image->hashName();

        }//end of if

        $user = User::create($request_data);
        deleteOldFiles("uploads/admins/" . $user->id . "/profile");

        if ($request->image) {
            $user->update(["image" => uploadImage($request->image, "uploads/admins/" . $user->id . "/profile")]);
        }
        $user->attachRole('super_admin');
//        $user->syncPermissions($request->permissions);
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.users.index');

    }//end of store

    public
    function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));

    }//end of user

    public
    function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($user->id),],
            'image' => 'image',
            'permissions' => 'required|min:1'
        ]);

        $request_data = $request->except(['permissions', 'image']);

        if ($request->image) {

            if ($user->image != 'default.png') {

                Storage::disk('public_uploads')->delete('/user_images/' . $user->image);

            }//end of inner if

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('uploads/user_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();

        }//end of external if

        $user->update($request_data);

        $user->syncPermissions($request->permissions);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.users.index');

    }//end of update

    public
    function destroy(User $user)
    {
        if ($user->image != 'default.png') {
            Storage::disk('public_uploads')->delete('/user_images/' . $user->image);
        }//end of if

        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');

    }//end of destroy

}//end of controller
