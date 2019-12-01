<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\MainApp\Notifications\DueNotif;
use Facades\hpsynapse\moduser\Repositories\UserRepo;
use Facades\App\MainApp\Repositories\Pengajuan;

use Carbon\Carbon;

class DueNotification //implements ShouldQueue
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
        $list = Pengajuan::listAlmostFinishPengajuan();
        foreach ($list as $key => $value) {
            $mitra = false;
            foreach ($value['mitra'] as $key => $mitraItem) {
                if($mitraItem['mitra_id'] == $value['mitra_id']){
                    $gl = Carbon::parse($value['spk']['tanggal'])->addDays($mitraItem['batas_penyerahan']);
                    $tglMulaiNotif = now()->subDays(3);
                    if($gl->greaterThanOrEqualTo($tglMulaiNotif)){
                        $mitra = $mitraItem;
                    }
                }
            } 
            
            if($mitra){
                $user = [
                    'spv_id' => $value['pembuat_id'],
                    'manager_bag_id' => $value['manager_bag_id'],
                    'manager_id' => $value['manager_id'],
                    'renev_id' => $value['rpbj']['renev_id'],
                    'enjin_id' => $value['rpbj']['enjin_id'],
                    'laksdan_id' => $value['dpl']['laksdan_id'],
                ];
                foreach ($user as $userField => $userId) {
                    UserRepo::notify($userId,new DueNotif($value));
                }
            }
        }
        
    }
}
