<?php

namespace App\Services\export;

use Exception;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

use App\Models\Job;

use App\Facades\Excel as FExcel;
use App\Facades\Tenant;

Use App\Jobs\Export as JExport;

use App\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Driver export menggunakan Spout
 */
class Excel
{ 
    protected $driver;
    protected $columnHeader = [];
    public $letterMap = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G'
		,8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R'
        ,19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z');

    public function __construct($driver='phpspreadsheet')
    {
        if($driver=='spout'){
            $this->driver = New \App\Services\export\driver\Spout();
        }else{
            $this->driver = New \App\Services\export\driver\PhpSpreadsheet();
        }
    }
    /**
	 * excel colom, ubah angka kolom ke kolom excel
	 */
	public function excol(int $int=0){
		$hasil = array();
		
		while ($s1 = floor($int/26)){
			$s2 = $int % 26;
			if($s1>=1){
				if(isset($this->letterMap[$s2])){
					array_unshift($hasil,$this->letterMap[$s2]);
					$int = $s1;
				}else{
					array_unshift($hasil,'Z');
					$int = $s1-1;
				}
				
			}else{
				$int = 26;
				break;
			}
		}
		if(isset($this->letterMap[$int]))array_unshift($hasil,$this->letterMap[$int]);
		
		$hasil = implode('', $hasil);
		return $hasil;
    }

    public function load($template, string $format = 'Xls', bool $mainAppDoc = true)
    {
        return $this->driver->load($template, $format, $mainAppDoc);
    }
    
    public function insertRow(&$reader,$row, $templateVar){
        return $this->driver->insertRow($reader,$row, $templateVar);
    }
    /**
     * write data ke berdasarkan cell nya
     */
    public function setCell(&$reader,$data)
    {
        return $this->driver->setCell($reader,$data);
    }
}