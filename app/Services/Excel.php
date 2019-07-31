<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
// use PhpOffice\PhpSpreadsheet\Writer\WXlsx;
// use PhpOffice\PhpSpreadsheet\Reader\RXlsx;

class Excel
{
    public $TBS;

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