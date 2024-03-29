<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ChatController extends Controller
{
    public function index(){
//        $chatIds = ChatMessage::latest()->pluck("from_user_id")
//            ->merge(ChatMessage::latest()->pluck("to_user_id"))
//            ->unique()
//            ->toArray();
//        $users = User::whereIn("id",$chatIds)->where("role","!=","super_admin")->paginate(50);

        $chatIds = ChatMessage::latest()->pluck("from_user_id")
            ->merge(ChatMessage::latest()->pluck("to_user_id"))
            ->unique()
            ->toArray();

        $users = User::whereIn("id", $chatIds)
            ->where("role", "!=", "super_admin")
            ->orderByRaw(DB::raw("FIELD(id, " . implode(',', $chatIds) . ")"))
            ->get();
        return view("dashboard.chats.index",compact("users"));





//        $super_admin_ids = User::where('role', 'super_admin')->pluck("id")->toArray();
//        $chats = ChatMessage::join('users as from_user', 'from_user.id', '=', 'chat_messages.from_user_id')
//            ->join('users as to_user', 'to_user.id', '=', 'chat_messages.to_user_id')
//            ->whereIn('from_user.id', function ($query) use ($super_admin_ids){
//                $query->select('id')
//                    ->from('users')
//                    ->whereNotIn('id',  $super_admin_ids);
//            })
//            ->orWhereIn('to_user.id', function ($query) use ($super_admin_ids) {
//                $query->select('id')
//                    ->from('users')
//                    ->where('role', $super_admin_ids);
//            })
//            ->latest('chat_messages.created_at')
//            ->paginate(50);
//        return view("dashboard.chats.index",compact("chats"));


        return view("dashboard.chats.index", compact("users"));

    }
    public function getMassages(Request $request){
        Carbon::setLocale(app()->getLocale());
        $chats = ChatMessage::where("from_user_id",$request->user_id)->orWhere("to_user_id",$request->user_id)->latest()->paginate(20);
        $countUnReadMessages = ChatMessage::where("from_user_id", $request->user_id )->where("status", "notReaded")
            ->update(["status" => "readed"]);
        if ($chats->lastPage() == 1){
            $next_page_url = "";
        }else{
            $next_page_url =$chats->path()."?user_id=".$request->user_id."&page=1"; // Get the items for the current page
        }
        $user_id = $request->user_id;
        if ($request->wantsJson()){
            Carbon::setLocale(app()->getLocale());
            $messages = ChatMessage::where("from_user_id",$request->user_id)->orWhere("to_user_id",$request->user_id)->latest()->paginate(20);
            return ChatResource::collection($messages);
        }
        return view("dashboard.chats.view",compact("chats","user_id","next_page_url"));

    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            "message" => ["required", "string", "max:191"],
        ]);
        try {
            $admin = User::where("role" ,"super_admin")->where("status","active")->first();
            $user = User::where("id",$request->to_user_id)->first();
            $chat = ChatMessage::query()->create([
                "message" => $request->message,
                "from_user_id" =>$admin->id ,
                "to_user_id" => $request->to_user_id,
                "created_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
                "updated_at" => Carbon::now()->timezone('Africa/Cairo')->toDateTimeString(),
            ]);

            $data["id"] = $chat->id;
            $data["direct"] = "right";
            $data["name"] = "المركبة";
            $data["image"] = $admin->image;
            $data["message"] = $request->message;
            $data["status"] = "notReaded";
            $data["sent_at"] =Carbon::now()->timezone('Africa/Cairo')->diffForHumans() ;

            $result = false;
            if(!is_null($user->device_token)){
                $da["id"] = $chat->id;
                $da["direct"] = "left";
                $da["name"] = "المركبة";
                $da["image"] = "http://el-markaba.kirellos.com/uploads/student/28/profile/student_profile_image_1690881214.";
                $da["message"] = $request->message;
                $da["status"] = "notReaded";
                $da["sent_at"] =Carbon::now()->timezone('Africa/Cairo')->diffForHumans();
                $result = send_fcm([$user->device_token],"مركز المركبة",$request->message,"receiveMessage",$da);
            }
            Notification::create([
                "type" => "receiveMessage",
                "title" => __("site.markz_el_markaba"),
                "body" => $request->message,
                "read" => "0",
                "model_id" => $user->id,
                "model_json" => $user,
                "user_id" => $user->id,
                "fcm" => $result,
            ]);
            return api_response(1,__("site.message sent successfully"),$data);
        }catch (\Exception $exception){
            return api_response(0,$exception->getMessage(),"");
        }
    }

}//end of controller
