<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Writer\WXlsx;
// use PhpOffice\PhpSpreadsheet\Reader\RXlsx;

class Excel
{
    public $TBS;
    public $letterMap = array(1=>'A',2=>'B',3=>'C',4=>'D',5=>'E',6=>'F',7=>'G'
		,8=>'H',9=>'I',10=>'J',11=>'K',12=>'L',13=>'M',14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R'
        ,19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z');

    /**
	 * excel colom, ubah angka kolom ke kolom excel
	 */
	public function excol($int=false){
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
    public function load($template, $format = 'Xls')
	{
        $filepathTemplate = app_path('MainApp/resources/doc/'.$template);
        $reader = IOFactory::createReader($format);
        $reader = $reader->load($filepathTemplate);//::createReader("Xlsx")

        return $reader;        
    }

    public function setCell($reader,$data){
        foreach ($data as $key => $value) {
            if(is_string($value)){             
                $reader->getActiveSheet()->setCellValueExplicit($key, $value,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);   
                if(strpos($value,'-')===0){
                    $reader->getActiveSheet()->getStyle($key)->setQuotePrefix(true);
                }
            }else{
                $reader->getActiveSheet()->setCellValue($key, $value);
            }
            
        }
        return $reader;
    }

    public function insertRow($reader,$row, $templateVar){
        $reader->getActiveSheet()->insertNewRowBefore($row, 1);
        $newvar = [];
        foreach ($templateVar as $key => $value) {
            $newvar[$key.$row] = $value;
        }
        return $this->setCell($reader,$newvar);
    }

    public function download($reader){
        $writer = IOFactory::createWriter($reader, 'Xlsx');
		$writer->save('php://output'); // download file
    }
}