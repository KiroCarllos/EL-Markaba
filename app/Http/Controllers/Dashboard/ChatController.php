<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\ChatMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class ChatController extends Controller
{
    public function index(){
        $chat_from_user_ids = ChatMessage::pluck("from_user_id")->toArray();
        $chat_to_user_ids = ChatMessage::pluck("to_user_id")->toArray();
        $chat_ids = array_unique(array_merge($chat_from_user_ids,$chat_to_user_ids));
        $users = User::whereIn("id",$chat_ids)->where("role","!=","super_admin")->paginate(50);
        return view("dashboard.chats.index",compact("users"));
    }
    public function getMassages(Request $request){
        Carbon::setLocale(app()->getLocale());
        $chats = ChatMessage::where("from_user_id",$request->user_id)->orWhere("to_user_id",$request->user_id)->paginate(100);
        $user_id = $request->user_id;
        return view("dashboard.chats.view",compact("chats","user_id"));

    }

}//end of controller
