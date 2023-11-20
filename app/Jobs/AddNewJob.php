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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $data;
    public $job;
    public function __construct($data,$job)
    {
        $this->data = $data;
        $this->job = $job;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->data as $recipient){
//            $result = send_fcm([$recipient->device_token],__("site.markz_el_markaba"),__("site.new_job_added"),"jobs",$this->job);
            $result = send_fcm(["e3j3KtGWQpiJmDzeSYB5Wb:APA91bGIFbaWFn0OX4zyGlysHRq4PGp2jm-oMIFi_MqRNdMYB6AmgNsQdeLzXJUtLgRuHCbI7b054x-rj5mg8mrRxS7L8zNGx7O_FmDiTclNMKYCWIZbvSnw5U9Ynu47bWCkJQ3yJH_k"],__("site.markz_el_markaba"),__("site.new_job_added"),"jobs",$this->job);
            Notification::create([
                "type" => "newJob",
                "title" => __("site.markz_el_markaba"),
                "body" => __("site.new_job_added"),
                "read" => "0",
                "model_id" => $this->job->id,
                "model_json" => $this->job,
//                "user_id" => $this->job->id,
                "user_id" => 31,
                "fcm" => $result,
            ]);
        }
    }
}
