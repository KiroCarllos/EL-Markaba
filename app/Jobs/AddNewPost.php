<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $postModel;

    public function __construct($data,$post)
    {
        $this->users = $data;
        $this->postModel = $post;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $recipient){
            $result = send_fcm([$recipient->device_token],__("site.markz_el_markaba"),__("site.you_has_add_post_successfully"),"posts",$this->postModel);
            Notification::create([
                "type" => "newAccount",
                "title" => __("site.markz_el_markaba"),
                "body" => __("site.you_has_add_post_successfully"),
                "read" => "0",
                "model_id" => $this->postModel->id,
                "model_json" => $this->postModel,
                "user_id" => $recipient->id,
                "fcm" => $result,
            ]);
        }
    }
}
//            foreach ($recipients as $recipient){
//                $result = send_fcm([$recipient->device_token],__("site.markz_el_markaba"),__("site.you_has_add_post_successfully"),"posts",$post);
//                Notification::create([
//                    "type" => "newAccount",
//                    "title" => __("site.markz_el_markaba"),
//                    "body" => __("site.you_has_add_post_successfully"),
//                    "read" => "0",
//                    "model_id" => $post->id,
//                    "model_json" => $post,
//                    "user_id" => $recipient->id,
//                    "fcm" => $result,
//                ]);
//            }
