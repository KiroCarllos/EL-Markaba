<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command("queue:work --stop-when-empty")->everyFiveMinutes()->withoutOverlapping();
//        $schedule->command("queue:restart")->everyTenMinutes();

        if (!$this->osProcessIsRunning('queue:work')) {
//            $schedule->call('\App\Http\Controllers\Api\ServiceController@checkExpiredJobs')->withoutOverlapping();
            $schedule->command('queue:work')->everyMinute();
            $schedule->command('queue:restart')->everyFiveMinutes();
        }
        // $schedule->command('inspire')
        //          ->hourly();
    }
    protected function osProcessIsRunning($needle): bool
    {
        // get process status. the "-ww"-option is important to get the full output!
        exec('ps aux -ww', $process_status);

        // search $needle in process status
        $result = array_filter($process_status, function($var) use ($needle) {
            return strpos($var, $needle);
        });

        // if the result is not empty, the needle exists in running processes
        if (!empty($result)) {
            return true;
        }
        return false;
    }
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
