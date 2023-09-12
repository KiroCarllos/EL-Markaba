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

        ];
    }
}
