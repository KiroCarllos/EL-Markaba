<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminCanChat;
use App\Models\ChatMessage;
use App\Models\CompanyDetail;
use App\Models\Faculty;
use App\Models\Job;
use App\Models\JobApplication;
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

class ChatController extends Controller
{
    public function admins()
    {
        $admin_ids = AdminCanChat::where("status","can chat")->pluck("user_id")->toArray();
        $admins = User::select("id","name")->where("role","super_admin")->where("status","active")->whereIn("id",$admin_ids)->get();
        return api_response(1,"",$admins);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            "message" => ["required", "string", "max:191"],
//            "admin_id" => ["required", "numeric", Rule::exists("users","id")->where("role","super_admin")->where("status","active")]
        ]);
        try {
            $chat = ChatMessage::query()->create([
                "message" => $request->message,
                "from_user_id" => auth("api")->id(),
                "to_user_id" => User::where("role" ,"super_admin")->where("status","active")->pluck("id")->first(),
                "created_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
                "updated_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
            ]);
            return api_response(1,__("site.message sent successfully"));
        }catch (\Exception $exception){
            return api_response(0,$exception->getMessage(),"");
        }
    }

    public function getMyMessages()
    {
        Carbon::setLocale(app()->getLocale());
        $messages = ChatMessage::where("from_user_id",auth()->id())->orWhere("to_user_id",auth()->id())->latest()->get();
        foreach ($messages as $index=>$obj){
            $data[$index]["id"] = $obj->id;
            $data[$index]["direct"] = $obj->from_user_id == auth()->id() ?"left":"right";
            $data[$index]["name"] =  $obj->fromUser->name;
            $data[$index]["image"] =  $obj->fromUser->image ;
            $data[$index]["message"] = $obj->message;
            $data[$index]["status"] = $obj->status;
            $data[$index]["sent_at"] = Carbon::parse($obj->created_at)->diffForHumans();
        }
        return api_response(1,"",$data);

    }
}//end of controller
