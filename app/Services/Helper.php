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

    function file_get_contents_curl( $url ) {

        $ch = curl_init();
      
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
      
        $data = curl_exec( $ch );

        curl_close( $ch );
        return $data;
      
    }
    
    public function terbilang($nilai) {
		$nilai = abs($nilai);
		$huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
		$temp = "";
		if ($nilai < 12) {
			$temp = " ". $huruf[$nilai];
		} else if ($nilai <20) {
			$temp = $this->terbilang($nilai - 10). " Belas";
		} else if ($nilai < 100) {
			$temp = $this->terbilang($nilai/10)." Puluh". $this->terbilang($nilai % 10);
		} else if ($nilai < 200) {
			$temp = " Seratus" . $this->terbilang($nilai - 100);
		} else if ($nilai < 1000) {
			$temp = $this->terbilang($nilai/100) . " Ratus" . $this->terbilang($nilai % 100);
		} else if ($nilai < 2000) {
			$temp = " Seribu" . $this->terbilang($nilai - 1000);
		} else if ($nilai < 1000000) {
			$temp = $this->terbilang($nilai/1000) . " Ribu" . $this->terbilang($nilai % 1000);
		} else if ($nilai < 1000000000) {
			$temp = $this->terbilang($nilai/1000000) . " Juta" . $this->terbilang($nilai % 1000000);
		} else if ($nilai < 1000000000000) {
			$temp = $this->terbilang($nilai/1000000000) . " Milyar" . $this->terbilang(fmod($nilai,1000000000));
		} else if ($nilai < 1000000000000000) {
			$temp = $this->terbilang($nilai/1000000000000) . " Trilyun" . $this->terbilang(fmod($nilai,1000000000000));
		}     
		return $temp;
	}
    
    /**
     * BC MATH
     */

    
    /**
     * penambahan
     */
    public static function bcadd($num1,$num2)
    {
        $num1 = self::bcToString($num1);
        $num2 = self::bcToString($num2);
        return rtrim(rtrim(bcadd($num1,$num2),'0'),'.')?:'0';
    }

    /**
     * pengurangan
     */
    public static function bcsub($num1,$num2)
    {
        $num1 = self::bcToString($num1);
        $num2 = self::bcToString($num2);
        return rtrim(rtrim(bcsub($num1,$num2),'0'),'.')?:'0';
    }

    /**
     * perkalian
     */
    public static function bcmul($num1,$num2)
    {
        $num1 = self::bcToString($num1);
        $num2 = self::bcToString($num2);
        return rtrim(rtrim(bcmul($num1,$num2),'0'),'.')?:'0';
    }
    
    /**
     * pembagian
     */
    public static function bcdiv($num1,$num2)
    {
        $num1 = self::bcToString($num1);
        $num2 = self::bcToString($num2);
        return rtrim(rtrim(bcdiv($num1,$num2),'0'),'.')?:'0';
    }

    /**
     * untuk memastikan jika ada scientifik notation akan diconvert ke decimal biasa
     */
    public static function bcToString($num)
    {
        if(is_string($num))$num = (float) $num;        
        $num = rtrim(sprintf("%.20f", $num), "0");
        return rtrim($num,'.')?:'0';
    }
}