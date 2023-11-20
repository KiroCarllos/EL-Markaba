<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewTraining implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $users;
    public $trainingModel;

    public function __construct($data,$training)
    {
        $this->users = $data;
        $this->trainingModel = $training;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->users as $recipient){
            $result = send_fcm([$recipient->device_token],__("site.markz_el_markaba"),__("site.you_has_add_training_successfully"),"trainings",$this->trainingModel);
            Notification::create([
                "type" => "newAccount",
                "title" => __("site.markz_el_markaba"),
                "body" => __("site.you_has_add_training_successfully"),
                "read" => "0",
                "model_id" => $this->trainingModel->id,
                "model_json" => $this->trainingModel,
                "user_id" => $recipient->id,
                "fcm" => $result,
            ]);
        }
    }
}
