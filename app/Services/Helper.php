<?php

namespace App\Services;
use Carbon\Carbon;
class Helper
{    
    protected $bulan = [
        1=>'Januari',
        2=>'Februari',
        3=>'Maret',
        4=>'April',
        5=>'Mei',
        6=>'Juni',
        7=>'Juli',
        8=>'Agustus',
        9=>'September',
        10=>'Oktober',
        11=>'November',
        12=>'Desember'
    ];

    public function indDateFormat($dateString) {
        $date = new Carbon($dateString);
        return $date->day.' '.$this->bulan[$date->month].' '.$date->year;
    }
}