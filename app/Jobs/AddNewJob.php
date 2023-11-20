<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddNewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;
    public $jobModel;
    public function __construct($data,$job)
    {
        $this->users = $data;
        $this->jobModel = $job;

    }

    public function handle()
    {
        foreach ($this->users as $recipient){
            $result = send_fcm([$recipient->device_token],__("site.markz_el_markaba"),__("site.new_job_added"),"jobs",$this->job);
            Notification::create([
                "type" => "newJob",
                "title" => __("site.markz_el_markaba"),
                "body" => __("site.new_job_added"),
                "read" => "0",
                "model_id" => $this->job->id,
                "model_json" => $this->job,
                "user_id" => $recipient->id,
                "fcm" => $result,
            ]);
        }
    }
}
