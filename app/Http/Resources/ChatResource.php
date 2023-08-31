<?php

namespace App\Http\Resources;

use App\Models\ChatMessage;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
                "id" => $this->id,
                "direct" => $this->from_user_id == auth()->id() ?"right":"left",
                "name" => $this->fromUser->name,
                "image" => $this->fromUser->image,
                "message" => $this->message,
                "status" => $this->status,
                "sent_at" => Carbon::parse($this->created_at)->diffForHumans(),
//            $data[$index]["id"] = $obj->id;
//            $data[$index]["direct"] = $obj->from_user_id == auth()->id() ?"right":"left";
//            $data[$index]["name"] =  $obj->fromUser->name;
//            $data[$index]["image"] =  $obj->fromUser->image;
//            $data[$index]["message"] = $obj->message;
//            $data[$index]["status"] = $obj->status;
//            $data[$index]["sent_at"] = Carbon::parse($obj->created_at)->diffForHumans();

        ];
    }
}
