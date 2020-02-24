<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Services\Utilities;

/**
 * Jobs untuk update role dari file json di /app/MainApp/config/acl/*
 */
class UpdateAcl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $connection;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($connection=false)
    {
        $this->connection = $connection?$connection:config('database.default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = 'moduser:aclupdate --connection='.$this->connection;
        Utilities::artisan($command);        
    }
}
