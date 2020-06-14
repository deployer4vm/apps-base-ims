<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


use App\Services\Utilities;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;


class PruneTelescope implements ShouldQueue
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
        $now = now()->format('Y_m_d_H_i_s');

        // backup table telescope_entries
        $query = 'CREATE TABLE BC_telescope_entries_'.$now.' LIKE telescope_entries';
        DB::statement($query);
        $query = 'INSERT INTO BC_telescope_entries_'.$now.' SELECT * FROM telescope_entries';
        DB::statement($query);

        // backup table telescope_entries_tags
        $query = 'CREATE TABLE BC_telescope_entries_tags_'.$now.' LIKE telescope_entries_tags';
        DB::statement($query);
        $query = 'INSERT INTO BC_telescope_entries_tags_'.$now.' SELECT * FROM telescope_entries_tags';
        DB::statement($query);
        
        // backup table telescope_monitoring
        $query = 'CREATE TABLE BC_telescope_monitoring_'.$now.' LIKE telescope_monitoring';
        DB::statement($query);
        $query = 'INSERT INTO BC_telescope_monitoring_'.$now.' SELECT * FROM telescope_monitoring';
        DB::statement($query);

        $command = 'telescope:prune';
        Utilities::artisan($command);  

    }
}
