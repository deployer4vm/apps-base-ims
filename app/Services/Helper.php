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

    protected $hari = [
        'Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'
    ];

    public function indDateFormat($dateString) {
        $date = new Carbon($dateString);
        return $date->day.' '.(isset($this->bulan[$date->month])?$this->bulan[$date->month]:'').' '.$date->year;
    }

    public function indDay($dateString) {
        $date = new Carbon($dateString);
        return isset($this->hari[$date->dayOfWeek])?$this->hari[$date->dayOfWeek]:'';
    }

    public function indMonth($dateString) {
        $date = new Carbon($dateString);
        return isset($this->bulan[$date->month])?$this->bulan[$date->month]:'';
    }

    function numberToRoman($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    /**
     * 
     */
    
    public function terbilang($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->terbilang($nilai - 10). " belas";
		} else if ($nilai < 100) {
			$temp = $this->terbilang($nilai/10)." puluh". $this->terbilang($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " seratus" . $this->terbilang($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->terbilang($nilai/100) . " ratus" . $this->terbilang($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " seribu" . $this->terbilang($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->terbilang($nilai/1000) . " ribu" . $this->terbilang($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->terbilang($nilai/1000000) . " juta" . $this->terbilang($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->terbilang($nilai/1000000000) . " milyar" . $this->terbilang(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->terbilang($nilai/1000000000000) . " trilyun" . $this->terbilang(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
}