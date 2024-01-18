<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\FatherExport;
use App\Exports\StudentExport;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\GeneralController;
use App\Models\Area;
use App\Models\ChatMessage;
use App\Models\FatherDetail;
use App\Models\Notification;
use App\Models\University;
use App\Models\User;
use App\Models\StudentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class FatherController extends Controller
{
    public function index(Request $request)
    {
        $fathers = User::Father()->whereHas("father_details")->latest()->get();
        return view('dashboard.fathers.index', compact('fathers'));
    }//end of index

    public function create()
    {
        $areas = Area::all();
        return view('dashboard.fathers.create', compact("areas"));

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string|size:11|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg|max:4096',
            'national_image' => 'required|mimes:jpeg,png,jpg|max:4096',
            'area_id' => ['required', Rule::exists(Area::class, "id")],
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
                "role" => "father",
                "status" => "active",
            ]);
            deleteOldFiles("uploads/fathers/" . $user->id . "/profile");
            if ($request->image) {
                $user->update(["image" => uploadImage($request->image, "uploads/fathers/" . $user->id . "/profile")]);
            }


            $user->attachRole('father');
            $fatherData = $request->only(["area_id"]);

            $fatherData = FatherDetail::query()->updateOrCreate([
                "user_id" => $user->id
            ], $fatherData);
            deleteOldFiles("uploads/fathers/" . $user->id . "/national_image");
            if ($request->image) {
                $fatherData->update(["national_image" => uploadImage($request->national_image, "uploads/fathers/" . $user->id . "/national_image")]);
            }
            DB::commit();
            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.fathers.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            return api_response(0, $exception);
        }
    }//end of store

    public function edit($id)
    {
        $areas = Area::all();
        $father = User::whereId($id)->with("father_details")->first();
        return view('dashboard.fathers.edit', compact('father', 'areas'));
    } //end of user

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'mobile' => ['required', "size:11", Rule::unique('users', 'mobile')->ignore($request->user_id)],
            'email' => ['required', "email", Rule::unique('users', 'email')->ignore($request->user_id)],
            'password' => 'nullable',
            'image' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'national_image' => 'nullable|mimes:jpeg,png,jpg|max:4096',
            'area_id' => ['required', Rule::exists(Area::class, "id")],
        ]);

        $userData = $request->only(["name", "mobile", "email", "status"]);
        if ($request->has("password") && !is_null($request->password)) {
            $userData["password"] = Hash::make($request->password);
        }

        try {
            DB::beginTransaction();
            $user = User::query()->whereId($id)->first();
            if (($user->status != "active" && $request->status == "active")) {
                $result = send_fcm([$user->device_token], __("site.markz_el_markaba"), __("site.your_account_activated_can_make_login_now"), "posts", $user);
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => __("site.your_account_activated_can_make_login_now"),
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            $user->update($userData);
            if ($request->has("image") && !is_null($request->image)) {
                deleteOldFiles("uploads/fathers/" . $user->id . "/profile");
                $user->update(["image" => uploadImage($request->image, "uploads/fathers/" . $user->id . "/profile")]);
            }
            $fatherDetails = FatherDetail::whereUserId($id)->first();
            $fatherData = $request->only([ 'area_id']);
            $fatherDetails->update($fatherData);

            if ($request->has("notify") && !is_null($request->notify)) {
                $result = send_fcm([$user->device_token], __("site.markz_el_markaba"), $request->notify, "posts", $user);
                Notification::create([
                    "type" => "posts",
                    "title" => __("site.markz_el_markaba"),
                    "body" => $request->notify,
                    "read" => "0",
                    "model_id" => $user->id,
                    "model_json" => $user,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            if ($request->has("message") && !is_null($request->message)) {
                $admin = User::where("role", "super_admin")->where("status", "active")->first();
                $chat = ChatMessage::query()->create([
                    "message" => $request->message,
                    "from_user_id" => $admin->id,
                    "to_user_id" => $user->id,
                    "created_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
                    "updated_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
                ]);

                $data["id"] = $chat->id;
                $data["direct"] = "left";
                $data["name"] = "Super Admin";
                $data["image"] = "http://el-markaba.kirellos.com/uploads/student/28/profile/student_profile_image_1690881214.";
                $data["message"] = $request->message;
                $data["status"] = "notReaded";
                $data["sent_at"] = Carbon::now()->timezone('Africa/Cairo')->diffForHumans();

                $result = send_fcm([$user->device_token], __("site.markz_el_markaba"), $request->message, "receiveMessage", $data);
                Notification::create([
                    "type" => "receiveMessage",
                    "title" => __("site.markz_el_markaba"),
                    "body" => $request->message,
                    "read" => "0",
                    "model_id" => $chat->id,
                    "model_json" => $chat,
                    "user_id" => $user->id,
                    "fcm" => $result,
                ]);
            }
            if ($request->has("national_image") && !is_null($request->national_image)) {
                deleteOldFiles("uploads/fathers/" . $user->id . "/national_image");
                $fatherDetails->update(["national_image" => uploadImage($request->national_image, "uploads/fathers/" . $user->id . "/national_image")]);
            }
            DB::commit();
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.fathers.index');
        } catch (\Exception $exception) {
            DB::rollBack();
            dd($exception);
        }
    }//end of update

    public
    function destroy($id)
    {
        $userStudent = User::find($id);
        $userStudent->update(["status" => "deleted"]);
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.student_details.index');

    }//end of destroy


    public function export()
    {
        return Excel::download(new FatherExport(), 'fathers.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}//end of controller
