<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\File;
use App\Facades\Backup;
use Symfony\Component\Process\Process;

use Carbon\Carbon;


class GenerateBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $backupPath = storage_path('app/backups');
        $newBackupPath = $backupPath.DIRECTORY_SEPARATOR.'work'.DIRECTORY_SEPARATOR.$now;
        $uploadPath = public_path('upload');

        if (!File::isDirectory($newBackupPath)) {
            File::makeDirectory($newBackupPath, 0750, true);
        }
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0750, true);
        }

        $dump = new Process(
            ['mysqldump', '-u', $userName, $dbName],
            $newBackupPath,
            ['MYSQL_PWD' => (string) $password]
        );
        $dump->mustRun();
        File::put($newBackupPath.DIRECTORY_SEPARATOR.'db.sql', $dump->getOutput());

        (new Process([
            'tar', '-C', $uploadPath, '-zcf',
            $newBackupPath.DIRECTORY_SEPARATOR.'upload.tar.gz', '.'
        ]))->mustRun();

        $archivePath = $backupPath.DIRECTORY_SEPARATOR.$now.'.tar.gz';
        (new Process([
            'tar', '-C', $newBackupPath, '-zcf', $archivePath,
            'db.sql', 'upload.tar.gz'
        ]))->mustRun();

        File::deleteDirectory($newBackupPath);
        Backup::create([
            'backup_date'=>$now,
            'path'=>$archivePath,
            'status'=>1
        ]);

    }
}
