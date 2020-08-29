<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Facades\Backup;
use Exception;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use hpsynapse\moduser\Facades\UserAuth;

use Carbon\Carbon;

class RestoreBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tanggalBackup;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tanggalBackup)
    {
        $this->tanggalBackup = $tanggalBackup;
    }
    
    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {        
        UserAuth::unlockLogin();
        report($exception);          
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $now = now()->format('Y-m-d');
        $dbName = config('database.connections.mysql.database');
        $userName = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $backupPath = public_path('backup_file');
        $uploadPath = public_path('upload');
        $bcFilename = $this->tanggalBackup.'.tar.gz';
        $newBackupPath = $backupPath.'/'.$this->tanggalBackup;

        //extract backup data
        if(!file_exists($newBackupPath)){
            exec('mkdir "'.$newBackupPath.'"');
        }
        exec('cd "'.$backupPath.'" && tar -zxvf '.$bcFilename.' -C "'.$newBackupPath.'"');

        //delete semua file di /upload
        exec('cd "'.$uploadPath.'" && rm -rf * !(".gitignore")');
        //extract file backup upload
        exec('cd "'.$newBackupPath.'" && tar -zxvf upload.tar.gz -C "'.$uploadPath.'"');

        //drop semua table
        $tables = DB::select('SHOW TABLES');
        foreach($tables as $table){
            Schema::drop($table->{'Tables_in_'.$dbName});
        } 
        //restore semua database
        exec('cd "'.$newBackupPath.'" && mysql -u '.$userName.' -p"'.$password.'" $dbName < db.sql');
        //delete semua file 
        exec("rm -rf '".$newBackupPath."'");
        UserAuth::unlockLogin();
        Backup::update([['backup_date',$this->tanggalBackup],['status',2]],['status'=>1]);
    }
}
