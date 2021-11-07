<?php

namespace App\Services\export\driver;

use Exception;
use Carbon\Carbon;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Job;

use App\Facades\Excel;
use App\Facades\Tenant;

Use App\Jobs\Export as JExport;

/**
 * Driver export menggunakan Spout
 */
class Spout
{ 
    
    /**
     * Load / create file excel to write
     * @param string $template path dokumen
     * @param string $format format excel, "Xls" atau "Xlsx"
     * @param boolean $mainAppDoc true jika path MainApp/resources/doc/*
     */
    public function load($template, string $format = 'Xls', bool $mainAppDoc = true)
	{
        // $format = ucfirst(strtolower($format))=='Xls'?'Xls':'Xlsx';
        if(empty($template))
            return $this->create();

        if($mainAppDoc){
            $template = app_path('MainApp/resources/doc/'.$template);
        }
        $reader = WriterEntityFactory::createWriterFromFile($template);
        $reader->openToFile($template);

        return $reader;        
    }

    /**
     * create new spreadsheet
     */
    public function create()
    {
        return WriterEntityFactory::createXLSXWriter();
    }

    public function insertRow(&$reader,$row, $templateVar){
        $reader->getActiveSheet()->insertNewRowBefore($row, 1);
        $newvar = [];
        foreach ($templateVar as $key => $value) {
            $newvar[$key.$row] = $value;
        }
        return $this->setCell($reader,$newvar);
    }

    /**
     * write data ke berdasarkan cell nya
     */
    public function setCell(&$reader,$data){

        foreach ($data as $key => $value) {
            $option = ['quote'=>'auto','type'=>'default'];

            //jika array berarti menggunakan format sendiri
            if(is_array($value)){
                $option = isset($value['option'])?$value['option']:$option;
                $option['type'] = isset($value['option']['type'])?$value['option']['type']:'default';
                if(isset($value['option']['quote']))$option['quote'] = $value['option']['quote']?'yes':'no';
                $value = $value['value'];
            }else{
                $option['type'] = is_string($value)?'string':'default';
            }

            if($option['type']=='string'){
                //jika formula
                if(strpos($value,'=')===0){
                    $reader->getActiveSheet()->setCellValueExplicit(
                        $key, 
                        $value,
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA
                    );
                }else{
                    $this->setCellString($reader,$key,$value,$option['quote']);
                }
            }else if($option['type']=='number'){
                $reader->getActiveSheet()->setCellValueExplicit(
                    $key, 
                    $value,
                    \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                );
            }else if($option['type']=='percentage'){
                $digit = '';
                if(!isset($option['digit']))$option['digit']=0;
                if($option['digit']>0)
                    $digit = '.'.str_repeat('0',$option['digit']);
                $reader->getActiveSheet()->setCellValue($key, $value);
                $reader->getActiveSheet()
                    ->getStyle($key)
                    ->getNumberFormat()
                    ->setFormatCode('0'.$digit.'%;[Red]-0'.$digit.'%');
                    // ->applyFromArray([
                    //     "code" => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00
                    // ]);
            }else if($option['type']=='currency'){
                
                if(!isset($option['prefix']))$option['prefix']='';
                if(!isset($option['sufix']))$option['sufix']='';
                
                if($option['prefix']){
                    $format = '"'.$option['prefix'].'"#,##0.00_-';
                }else{
                    $format = '#,##0.00 "'.$option['sufix'].'"';
                }
                $reader->getActiveSheet()->setCellValue($key, $value);
                $reader->getActiveSheet()
                    ->getStyle($key)
                    ->getNumberFormat()
                    ->setFormatCode($format);
            }else{
                $reader->getActiveSheet()->setCellValue($key, $value);
            }            
        }

        //untuk memastikan memory langsung free tanpa nunggu gc
        $data = null;
        unset($data);

        return $reader;
    }

    private function setCellString(&$reader,$key,$value,string $quote='auto')
    {        
        //jika value diawali dengan - atau angka maka kasih quote
        if($quote!='no' && ($quote=='yes' || strpos($value,'-')===0 || preg_match('/^\d/', $value) === 1)){
            $value = "'".$value;
        }           

        $reader->getActiveSheet()->setCellValueExplicit(
            $key, 
            $value,
            \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
        ); 
        // $reader->getActiveSheet()->getStyle($key)->setQuotePrefix(true);
    }
}