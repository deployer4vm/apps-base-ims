<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;

// * * * * * cd /home/aplikasikop/koperasiv15/synapse/ && ea-php74 artisan synapse:runqueue >> /dev/null 2>&1

/**
 * run queue job without scheduler
 */
class RunQueue extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'synapse:runqueue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synapse - run queue job without scheduler';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pid = Cache::get('synapse:runqueue:pid',false);

        // jika sudah ada yg jalan maka keluar
        if($pid && posix_getpgid($pid)){
            exit();
        }else{
            Cache::forever('synapse:runqueue:pid',getmypid());
        }

        $lastRestart = Cache::get('illuminate:queue:restart');
        $workers = [];

        while (true) {
            $workers = array_values(array_filter($workers, function (Process $worker) {
                return $worker->isRunning();
            }));

            $jobs = \Illuminate\Support\Facades\DB::table('jobs')->orderBy('attempts','DESC')->orderBy('id','DESC')->get();
            $runningJobs = [];
            foreach ($jobs as $job) {
                if($job->attempts==1)
                    $runningJobs[$job->queue] = $job->queue;

                if($job->attempts!=1 && !isset($runningJobs[$job->queue])){
                    $runningJobs[$job->queue] = $job->queue;
                    if (!is_string($job->queue)
                        || !preg_match('/\A[A-Za-z0-9_.:-]{1,128}\z/D', $job->queue)) {
                        $this->warn('Skipped a job with an invalid queue name.');
                        continue;
                    }

                    $worker = new Process([
                        PHP_BINARY,
                        base_path('artisan'),
                        'queue:work',
                        '--queue='.$job->queue,
                        '--once',
                    ], base_path());
                    $worker->disableOutput();
                    $worker->start();
                    $workers[] = $worker;
                }
            }

            sleep(1);

            // detek apakah ada signal restart
            if($lastRestart != Cache::get('illuminate:queue:restart')){
                Cache::forever('synapse:runqueue:pid',false);
                exit();
            }
        }
    }
}
