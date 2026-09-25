<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Facades\Backup;

// use Exception;
use Throwable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

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
        if (!is_string($tanggalBackup) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $tanggalBackup)) {
            throw new \InvalidArgumentException('Backup date must use YYYY-MM-DD format.');
        }
        $this->tanggalBackup = $tanggalBackup;
    }
    
    /**
     * The job failed to process.
     *
     * @param  Throwable  $error
     * @return void
     */
    public function failed(Throwable $error)
    {        
        UserAuth::unlockLogin();
        report($error);          
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $dbName = config('database.connections.mysql.database');
        $userName = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $backupPath = storage_path('app/backups');
        $uploadPath = public_path('upload');
        $bcFilename = $this->tanggalBackup.'.tar.gz';
        $archivePath = $backupPath.DIRECTORY_SEPARATOR.$bcFilename;
        $newBackupPath = storage_path('app/restore/'.$this->tanggalBackup);

        if (!File::exists($archivePath)) {
            throw new \RuntimeException('Backup archive not found.');
        }

        if (File::isDirectory($newBackupPath)) {
            File::deleteDirectory($newBackupPath);
        }
        File::makeDirectory($newBackupPath, 0750, true);

        $archiveList = new Process(['tar', '-tzf', $archivePath]);
        $archiveList->mustRun();
        foreach (preg_split('/\r?\n/', trim($archiveList->getOutput())) as $entry) {
            if ($entry !== '' && ($entry[0] === '/' || preg_match('#(^|/)\.\.(/|$)#', $entry))) {
                throw new \RuntimeException('Unsafe path found in backup archive.');
            }
        }
        (new Process(['tar', '-xzf', $archivePath, '-C', $newBackupPath]))->mustRun();

        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0750, true);
        }
        foreach (File::directories($uploadPath) as $directory) {
            File::deleteDirectory($directory);
        }
        foreach (File::files($uploadPath) as $file) {
            if ($file->getFilename() !== '.gitignore') {
                File::delete($file->getPathname());
            }
        }
        (new Process([
            'tar', '-xzf', $newBackupPath.DIRECTORY_SEPARATOR.'upload.tar.gz',
            '-C', $uploadPath
        ]))->mustRun();

        //drop semua table
        $tables = DB::select('SHOW TABLES');
        foreach($tables as $table){
            Schema::drop($table->{'Tables_in_'.$dbName});
        } 
        $restore = new Process(
            ['mysql', '-u', $userName, $dbName],
            $newBackupPath,
            ['MYSQL_PWD' => (string) $password]
        );
        $restore->setInput(File::get($newBackupPath.DIRECTORY_SEPARATOR.'db.sql'));
        $restore->mustRun();

        File::deleteDirectory($newBackupPath);
        UserAuth::unlockLogin();
        Backup::update([['backup_date',$this->tanggalBackup],['status',2]],['status'=>1]);
    }
}
