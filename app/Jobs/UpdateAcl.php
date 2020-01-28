<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class UpdateAcl //implements ShouldQueue
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
        if (!Schema::connection($this->connection)->hasTable('koperasi_grup_detail')) {
            $roles = DB::connection($this->connection)->table('roles')->get();
            
            foreach ($roles as $value) {
                $roleFilename = app_path('MainApp/config/acl/'.$value->role_code.'.json');
                if(file_exists($roleFilename)){
                    $rule = file_get_contents($roleFilename);
                    DB::connection($this->connection)->table('roles')->where('role_code',$value->role_code)->update([
                        'rule' => $rule
                    ]);
                }
            }
        }
        
    }
}
