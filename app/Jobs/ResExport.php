<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Exception;
use App\MainApp\Modules\UnitToko\Facades\StockOpname;

/**
 * Bagian dari general Excel Export functionality (ResExportTraits)
 * opsi handling proses export excel menggunakan background proses
 */
class ResExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $repo,$addsJobsParam,$homeUrl;
    public $tries = 1;
    
    /**
     * Create a new job instance.
     *
     * @param ResExport $repo instance RestExport
     * @param array $addsJobsParam tambah parameter yang akan di passing ke initExportOnJon
     * @param string $homeUrl url lengkap ke home index
     * 
     * @return void
     */
    public function __construct($repo,array $addsJobsParam = [],string $homeUrl='')
    {
        $this->repo = $repo;
        $this->addsJobsParam = $addsJobsParam;
        $this->homeUrl = $homeUrl;
    }

    public function failed(Exception $exception)
    {        
        $repo = new $this->repo;        
        $repo->initExportOnJob($this->addsJobsParam,$this->homeUrl);
        $repo->setExportHomeUrl($this->homeUrl);
        $repo->setExportJobFailed($exception);
    }
    
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $repo = new $this->repo;
        $repo->initExportOnJob($this->addsJobsParam);
        $repo->setExportHomeUrl($this->homeUrl);
        $repo->processExport();
    }
}
