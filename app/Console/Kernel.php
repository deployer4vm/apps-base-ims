<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Jobs\PruneTelescope;

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
        // $schedule->command('inspire')
        //          ->hourly();
        // * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
        // $schedule->command('queue:work --daemon --tries=1 --queue=')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --daemon --tries=1 --queue=verification,email')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --daemon --tries=1 --queue=high,default,low')->everyMinute()->withoutOverlapping();
        // Export Worker
        $schedule->command('queue:work --tries=1 --queue=export1')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export2')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export3')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export4')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export5')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export6')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export7')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export8')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export9')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=export10')->everyMinute()->withoutOverlapping();
        // Import Worker
        $schedule->command('queue:work --tries=1 --queue=import1')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=import2')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=import3')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=import4')->everyMinute()->withoutOverlapping();
        $schedule->command('queue:work --tries=1 --queue=import5')->everyMinute()->withoutOverlapping();
        
        // load queuetambahan jika ada, bisa digunakan untuk per tenant juga
        $queueAdds = config('AppConfig.system.jobs.queue_adds',[]);
        foreach($queueAdds as $queue){
            $schedule->command('queue:work --daemon --tries=1 --queue='.$queue)->everyMinute()->withoutOverlapping();
        }
        
        // load queue tambahan per tenant jika aktif
        if(config('AppConfig.system.jobs.multitenant_add',false)){
            $queueAdds = config('AppConfig.tenant',[]);
            foreach($queueAdds as $queue){
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.' --once')->everyMinute()->withoutOverlapping();

                // export worker per tenent
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export1')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export2')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export3')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export4')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export5')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export6')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export7')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export8')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export9')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'export10')->everyMinute()->withoutOverlapping();
                // import worker per tenant
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'import1')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'import2')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'import3')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'import4')->everyMinute()->withoutOverlapping();
                $schedule->command('queue:work --daemon --tries=1 --queue=tenant'.$queue.'import5')->everyMinute()->withoutOverlapping();
            }
        }

        // jika websockets aktif maka aktifkan
        if(config('AppConfig.packageLocal.moduser.broadcast.local_server_enabled')){
            $schedule->command('websockets:serve')->everyMinute()->withoutOverlapping();
        }

        // run telescope prune 1 minggu sekali (sunday at 00:00)
        $schedule->job(new PruneTelescope)->weekly()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(app_path('MainApp/Console/Commands'));
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
