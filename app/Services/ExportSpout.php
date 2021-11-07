<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\Border;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Base\BaseRepository;

use App\Facades\Excel;
use App\Facades\Tenant;

use App\Models\Job;

Use App\Jobs\ExportSpout as JExport;


class ExportSpout extends BaseRepository
{   
    protected $cacheActive = true;
    private $_exportUploadPath = '/export/';//default path ke upload relative dari public_path
    private $_defaultFilename = 'export_data';

    // 0 new process
    // 1 sudah diinput/dispatch ke jobs
    // 2 jobs sudah / sedang berjalan
    // 3 jobs selesai
    // 4 jobs gagal
    static $EXPORT_STATUS_NEW = 0;
    static $EXPORT_STATUS_DISPATCHED = 1;
    static $EXPORT_STATUS_ON_PROGRESS = 2;
    static $EXPORT_STATUS_SUCCESS = 3;
    static $EXPORT_STATUS_FAILED = 4;

    //
    protected $_mainCacheKeyGroup = 'synapse.export';//
    protected $_mainCacheKeyList = 'list';//
    protected $_mainCacheKeyDetailPrefix = 'detail.';//
    
    // format output file
    protected $outputHeaderStartRow = 1;//poisi baris header
    protected $outputHeader = [];//judul2 header

    /**
     * PUBLIC
     * -------------------------------------------------------------------------
     */

    /**
     * list seluruh export
     */
    public function listExport()
    {
        $list = $this->_getCache($this->_mainCacheKeyGroup,$this->_mainCacheKeyList,[]);
        return $list;
    }
    
    public function nextExportQueue()
    {        
        $idxQueue=1;
        while (Job::where('queue','export'.$idxQueue)->exists()) {
            $idxQueue++;
            // berarti semua penuh, maka tambahkan ke yg paling sedikit
            if($idxQueue>10){
                $ret = DB::select("SELECT * FROM (SELECT COUNT(*) AS 'jm_queue',`queue` FROM `jobs` WHERE `queue` LIKE 'export%' GROUP BY `queue`) AS tmp_table ORDER BY `jm_queue` ASC LIMIT 1");
                return isset($ret['queue'])?$ret['queue']:'export1';
            }
        }
        return 'export'.$idxQueue;
    }

    /**
     * get data export
     */
    public function getExport($cacheKey) 
    {
        // $this->_deleteCache(
        //     $this->_mainCacheKeyGroup,
        //     $this->_mainCacheKeyDetailPrefix.$cacheKey
        // );
        return $this->_getCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey,
            false
        );
    }

    /**
     * STEP 1 - yang harus diset pertama kali di controllernya (atau di tempat export ini dicreate/diinisasi)
     * 
     * membuat process export baru
     * 
     * @param String $cacheKey
     * @param String $listingModel     full class namespace model
     * @param Array $listingParams 
     *      filter Array *optional
     */
    public function createExport($cacheKey,$listingModel,$listingParams=[],$userId=0,$tenantId=0)
    {
        $tenantId = $tenantId?$tenantId:config('tenant.id',0);
        
        $exportList = $this->_getCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyList,
            []
        );

        // jika belum ada maka create
        if(!isset($exportList[$cacheKey])){
            $exportList[$cacheKey] = $cacheKey;

        // jika sudah ada pastikan tidak dalam proses
        }else{
            $exportData= $this->getExport($cacheKey);
            if($exportData==false)$exportData= $this->initExportStatus();
            
            // jika sedang dalam proses
            if(
                $exportData['status']==self::$EXPORT_STATUS_DISPATCHED || 
                $exportData['status']==self::$EXPORT_STATUS_ON_PROGRESS
            ){
                $this->error = 'Jobs already exists';
                return false;
            // delete file sebelumnya
            }else{
                Storage::delete($exportData['laravelFilepath']);
            }
        }

        // set baru export
        $exportData= $this->initExportStatus();
        $exportData['cacheKey'] = $cacheKey;
        $exportData['listingModel'] = $listingModel;
        $exportData['listingParams'] = $listingParams;
        $exportData['userId'] = $userId;
        $exportData['tenantId'] = $tenantId;

        // tambahkan ke list
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyList,
            $exportList
        );

        // tambah detail nya
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey,
            $exportData
        );

        return $exportData;
    }

    /**
     * set export driver - phpspreadsheet , spout
     */
    public function setDriver($cacheKey,$driver)
    {
        $exportData = $this->getExport($cacheKey); 
        if($exportData==false)return false;
        $exportData['driver'] = strtolower($driver)=='phpspreadsheet'?'phpspreadsheet':'spout';//tandai sebagai force cancle
        $this->updateExport($cacheKey,$exportData);
    }

        
    /**
     * dispatch export ke queue untuk pertama kali
     * dieksekusi setelah createExport dan set-set config
     */
    public function dispatchExport($cacheKey)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;
        
        $exportData['laravelPath'] = $this->_exportUploadPath.$exportData['userId'].'/';
        $exportData['path'] = Storage::path($exportData['laravelPath']);

        // pastikan folder tujuan ada
        if(!file_exists($exportData['path'])){
            mkdir($exportData['path'],0777,true);
        }

        $exportData['jobDispatchTime'] = now()->format('Y-m-d H:i:s');
        $exportData['status'] = self::$EXPORT_STATUS_DISPATCHED;
        
        if(empty($exportData['filename']))
            $exportData['filename'] = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_',$this->_defaultFilename.'_'.$exportData['inputTime'])).'.xlsx';

        $exportData['laravelFilepath'] = $exportData['laravelPath'].$exportData['filename']; 
        $exportData['filepath'] = $exportData['path'].$exportData['filename'];   

        $exportData['fileurl'] = url('upload'.$exportData['laravelFilepath']);
        
        // jika sudah ada maka rename
        $i=1;
        while (file_exists($exportData['filepath'])) {
            $exportData['filepath'] = $exportData['path'].$i.'_'.$exportData['filename'];
            $exportData['laravelFilepath'] = $exportData['laravelPath'].$i.'_'.$exportData['filename'];
            $i++;
        }

        $this->updateExport($cacheKey,$exportData);
        $queueName = $this->nextExportQueue();

        if($this->isExportJobsPerTenant($cacheKey)){
            JExport::dispatch($cacheKey,'tenant'.$exportData['tenantId'].$queueName)->onQueue('tenant'.$exportData['tenantId'].$queueName);
        }else{
            JExport::dispatch($cacheKey,$queueName)->onQueue($queueName);;
        }

        return $exportData;
    }

    /**
     * cancel export yg sedang berjalan
     */
    public function cancelExport($cacheKey)
    {
        $exportData = $this->getExport($cacheKey); 
        if($exportData==false)return false;
        $exportData['forceCancle'] = 1;//tandai sebagai force cancle
        $this->updateExport($cacheKey,$exportData);

        return true;
    }

    /**
     * delete log export dan file hasil exportnya
     */
    public function deleteExport($cacheKey)
    {

    }

    /**
     * FUNGSI CONFIG SETER SETELAH createExport
     * -------------------------------------------------------------------------
     */

    public function setFilename($cacheKey,$filename)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['filename'] = $filename;

        $this->updateExport($cacheKey,$exportData);

        return true;
    }

    /**
     * set judul kolomn
     * 
     * @param String $cacheKey
     * @param Array $columnCaption isi kolom sesuai urutan dengan format :
     *      [
     *          [
     *              'field_name',
     *              [
     *                  'default' => ISI DEFAULT VALUE,
     *                  'caption' => string label caption column nya
     *                  'type' => string tipe datanya
     *              ]
     *          ],
     *          ...
     *      ]
     */
    public function setColumn($cacheKey,array $columnCaption, $headerRow = 1)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['template']['headerCaption'] = $columnCaption;
        $exportData['template']['headerCaptionRow'] = $headerRow;
        
        $this->updateExport($cacheKey,$exportData);
        return true;
    }

    /**
     * set template excel yang digunnakan (jika ingin menggunakan custom template)
     */
    public function setTemplate($cacheKey,$templatePath, $dataStartRow = 2)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['template']['filepath'] = $templatePath;
        $exportData['template']['dataStartRow'] = $dataStartRow;
        
        $this->updateExport($cacheKey,$exportData);
        return true;
    }

    /**
     * set general parameter yang bisa digunakan untuk custom formating nantinya
     */
    public function setAddsParam($cacheKey,$addsParam)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['addsParam'] = $addsParam;
        
        $this->updateExport($cacheKey,$exportData);
        return true;
    }

    /**
     * OVERIDE MAIN PROCESS
     * method-method untuk mengganti method utama dalam pemrosesan data
     */

    public function setCoreMainLooping($cacheKey, string $coreMainLoopingClass, string $coreMainLoopingMethod)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['template']['coreMainLoopingMethod'] = [$coreMainLoopingClass,$coreMainLoopingMethod];
        
        $this->updateExport($cacheKey,$exportData);
        return true;
    }
    
    /**
     * set class dan method untuk memformat data per row
     */
    public function setCoreRowFormater($cacheKey, string $coreRowFormaterClass, string $coreRowFormaterMethod)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['template']['coreRowFormaterMethod'] = [$coreRowFormaterClass,$coreRowFormaterMethod];
        
        $this->updateExport($cacheKey,$exportData);
        return true;
    }
    
    /**
     * set class dan method untuk memformat excel $reader saat setelah beres semua
     */
    public function setCoreLastFormater($cacheKey, string $coreLastFormaterClass, string $coreLastFormaterMethod)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['template']['coreLastFormaterMethod'] = [$coreLastFormaterClass,$coreLastFormaterMethod];
        
        $this->updateExport($cacheKey,$exportData);
        return true;
    }

    /**
     * CORE - TIDAK DIAKSES / DIGUNAKAN DARI APLIKASI SECARA LANGSUNG
     * -------------------------------------------------------------------------
     */

    protected function initExportStatus()
    {  
        $exportData = [];

        $exportData['driver'] = 'spout';//phpspreadsheet , spout
        $exportData['jobsId'] = 0;//id table jobs
        $exportData['processId'] = 0;//id process
        $exportData['forceCancle'] = 0;//1 jika force cancel

        $exportData['cacheKey'] = '';
        $exportData['queue'] = '';

        $exportData['addsParam'] = [];//general additional parameter jika diperlukan
        $exportData['listingModel'] = '';//bisa array [class,static method]

        $exportData['listingParams'] = [];//format filter synapse yang digunakan untuk filter data nya
        $exportData['userId'] = 0;
        $exportData['tenantId'] = 0;

        $exportData['log'] = '';//log status

        $exportData['template'] = [
			//full template file export nya jika custom, kosong jika menggunakan default template
            'filepath'=>'',
			// format header caption khusus default template
            'headerCaption'=>[],
            'headerCaptionRow'=>1,
			// format data
            'dataStartRow'=>2,//baris pertama data diinsert

            'coreMainLoopingMethod' => [],// [class,static method]
			
			'coreRowFormaterMethod'=>[],// [class,static method]
			
			'coreLastFormaterMethod'=>[],// [class,static method]
        ];

        $exportData['filename'] = '';//nama file export nya
		
        $exportData['path'] = '';// fullpath folder ke tempate file export berada
        $exportData['filepath'] = '';// $exportData['path'].'/'.$exportData['filename']
		
        $exportData['laravelPath'] = '';// path format laravel (yg bisa digunakan ke storage ke folder ke tempat file export berada
        $exportData['laravelFilepath'] = '';// $exportData['path'].'/'.$exportData['filename']
		
        $exportData['fileurl'] = '';//full url exportnya

        $exportData['count'] = 0;//jumlah total record yang harus diproses
        $exportData['processedCount'] = 0;//jumlah record yg sudah diproses

        $exportData['inputTime'] = now()->format('Y-m-d H:i:s');//waktu export dicreate pertama kali
        $exportData['jobDispatchTime'] = '';//waktu pertama kali jobs diproses (saat ke status 1)
        $exportData['jobStartTime'] = '';//waktu jobs pertama pertama kali diproses (saat ke status 2)
        
        $exportData['isResumeJob'] = false;
        $exportData['resumeJobParam'] = [
            'jobDispatchTime' => '',
            'jobStartTime' => ''
        ];

        $exportData['status'] = 0;
        // status :
        // 0 new process
        // 1 sudah diinput ke jobs
        // 2 jobs sudah di dispatch (run) / sedang berjalan
        // 3 jobs selesai
        // 4 jobs gagal
        return $exportData;
    }

    protected function updateExport($cacheKey,$exportData) 
    {   
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey,
            $exportData
        );
    }

    protected function setExportDone($cacheKey)
    {
        $exportData = $this->getExport($cacheKey); 
        if($exportData==false)return false;

        $exportData['log'] .= '<br><b class="text-success">Export Done !</b><br>';
        $exportData['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $exportData['status'] = self::$EXPORT_STATUS_SUCCESS;//2: success
        $this->updateExport($cacheKey,$exportData); 
    }

    /**
     * diexekusi saat export gagal
     */
    protected function setExportFailed($cacheKey)
    {
        $exportData = $this->getExport($cacheKey); 
        if($exportData==false)return false;

        $exportData['log'] .= '<br><b class="text-danger">Export Failed !</b><br>';
        $exportData['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $exportData['filename'] = '';
        $exportData['fileurl'] = '';
        $exportData['status'] = self::$EXPORT_STATUS_FAILED;//4: failed

        $this->updateExport($cacheKey,$exportData); 
    }
    
    /**
     * set dari cronjob, jika cronjob ada uncaught error
     * 
     * @param Exception $exception instance Exception dari job failed
     */
    public function setExportJobFailed($cacheKey,Exception $exception)
    {
        $this->setExportFailed($cacheKey); 

        $log = "<br><b class='text-danger'>Jobs terminated !</b>\n<hr>\n\nError message :<br>\n";
        $log .= $exception->getMessage();
        $log .= '<hr>';
        $log .= str_replace("\n",'<br>', $exception->getTraceAsString());

        $this->appendExportLog($log);
        report($exception); //lanjutkan error ke login (meureun)
    }

    public function appendExportLog($cacheKey,string $log='')
    {       
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $exportData['log'] .= $log;
        $this->updateExport($cacheKey,$exportData);
    }

    /**
     * -------------------------------------------------------------------------
     */
    
    private function _initExportData($exportData,$curQueue)
    {
        // set teknikal
        $exportData['queue'] = $curQueue;
        $exportData['processId'] = getmypid();

        $jobs = Job::where('payload','LIKE','%\"'.$exportData['cacheKey'].'\\\\\"%')->get()->append(['formated_payload'])->toArray();
        foreach ($jobs as $value) {
            $exportData['jobsId'] = $value['id'];
            break;
        }

        return $exportData;
    }


    private function _checkAndCounter($cacheKey)
    {       
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)
            return false;
            
        if($exportData['forceCancle']==1){            
            $exportData['log'] .= '<br><b class="text-danger">Export Canceled !</b><br>';
            $exportData['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
            $exportData['filename'] = '';
            $exportData['fileurl'] = '';
            $exportData['status'] = self::$EXPORT_STATUS_FAILED;//4: failed
            $this->updateExport($cacheKey,$exportData);
            return false;
        }

        $exportData['log'] .= '. ';
        $this->updateExport($cacheKey,$exportData);
        return true;
    }
    /**
     * proses utama yang dieksekusi dari jobs
     */
    public function processExport($cacheKey,$curQueue='export1')
    {        
        ini_set('memory_limit','5524M');
        set_time_limit(0);
        
        $startTime = microtime(true);        

        /**
         * init status & var
         */
        $exportData = $this->getExport($cacheKey);
        $exportData['status'] = self::$EXPORT_STATUS_ON_PROGRESS;
        $exportData = $this->_initExportData($exportData,$curQueue);       

        $tmpFilename = storage_path('logs'.DIRECTORY_SEPARATOR.'export_tmp'.DIRECTORY_SEPARATOR.$exportData['cacheKey'].'_'.$exportData['jobsId'].'_'.now()->format('YmdHis').'.xlsx');
        $file = fopen($tmpFilename, 'w');  
        fclose($file);

        // jika resume dari jobs sebelumnya yang di split 
        if($exportData['isResumeJob']){

            $exportData['resumeJobParam']['jobStartTime'] = now()->format('Y-m-d H:i:s');
            $this->updateExport($cacheKey,$exportData);

            $this->appendExportLog($cacheKey,'<span class="text-info">Continueing process from previous jobs</span>...<br>');
            $this->appendExportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');
            
            // we need a reader to read the existing file...
            $reader = ReaderEntityFactory::createReaderFromFile($exportData['filepath']);
            $reader->setShouldFormatDates(true); // this is to be able to copy dates
            $reader->open($exportData['filepath']);

            // ... and a writer to create the new file
            $writer = WriterEntityFactory::createWriterFromFile($tmpFilename);
            $writer->openToFile($tmpFilename);
            
            if(is_array($exportData['listingModel'])){
                $data = $exportData['listingModel'][0]::{$exportData['listingModel'][1]}(
                    $exportData['listingParams']
                );
            }else{
                $data = new $exportData['listingModel'];            
                $data = $this->_filter($data,$exportData['listingParams']['filter']);
            }
                        
            $offset = $exportData['resumeJobParam']['lastTableRow'];
            $limit = $exportData['count']+1000;

            $deleteRow = $exportData['template']['dataStartRow'];//row yg harus didelete, kenapa didelete untuk memastikan style header tidak terbawa
            $GLOBALS['synapse_export_indexExcelRow'] = $exportData['resumeJobParam']['lastExcelRow'];
            $isFirstRow = false;//flag untuk penanda baris pertama dari data
            $GLOBALS['synapse_export_indexData'] = $exportData['resumeJobParam']['lastTableRow'];
            
        //jika jobs pertama maka
        }else{
            if(is_array($exportData['listingModel'])){
                $data = $exportData['listingModel'][0]::{$exportData['listingModel'][1]}(
                    $exportData['listingParams']
                );
            }else{
                $data = new $exportData['listingModel'];            
                $data = $this->_filter($data,$exportData['listingParams']['filter']);
            }            

            $exportData['count'] = $data->count();
            $exportData['jobStartTime'] = now()->format('Y-m-d H:i:s');
            
            $this->updateExport($cacheKey,$exportData); 

            $this->appendExportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');
            $this->appendExportLog($cacheKey,'Url will be at : '.$exportData['fileurl'].'<br>');
            


            $tmpFileReader = $exportData['template']['filepath']?$exportData['template']['filepath']:resource_path('doc/generalExport.xlsx');

            // we need a reader to read the existing file...
            $reader = ReaderEntityFactory::createReaderFromFile($tmpFileReader);
            $reader->setShouldFormatDates(true); // this is to be able to copy dates
            $reader->open($tmpFileReader);

            // ... and a writer to create the new file
            $writer = WriterEntityFactory::createWriterFromFile($tmpFilename);
            $writer->openToFile($tmpFilename);

            
            $GLOBALS['synapse_export_indexExcelRow']=$exportData['template']['dataStartRow'];//urutan baris excel    
            $deleteRow=$GLOBALS['synapse_export_indexExcelRow'];//row yg harus didelete, kenapa didelete untuk memastikan style header tidak terbawa
            $GLOBALS['synapse_export_indexExcelRow']++;//start row ditambah satu agar style header tidak terbawa, karena nanti first row ini akan didelete juga
            $GLOBALS['synapse_export_indexData'] = 1;//nomor urut data dari 1 dst
            $isFirstRow = true;//flag untuk penanda baris pertama dari data
            $offset = 0;
            $limit = null;
        }        

        
        // let's read the entire spreadsheet...
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            // Add sheets in the new file, as we read new sheets in the existing one
            if ($sheetIndex !== 1) {
                $writer->addNewSheetAndMakeItCurrent();
            }

            foreach ($sheet->getRowIterator() as $row) {
                // ... and copy each row into the new spreadsheet
                $writer->addRow($row);
            }
        }

        if(!is_array($exportData['listingModel']) && !empty($exportData['listingParams']['orderBy'])){

            if(!is_array($exportData['listingParams']['orderBy'][0]))
				$exportData['listingParams']['orderBy']=[$exportData['listingParams']['orderBy']];

            foreach($exportData['listingParams']['orderBy'] as $oBitem){
                $data = $data->orderBy($oBitem[0], $oBitem[1]);
            }
        }
        
        $border = (new BorderBuilder())
            ->setBorderTop(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderRight(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderBottom(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->setBorderLeft(Color::BLACK, Border::WIDTH_THIN, Border::STYLE_SOLID)
            ->build();
        
        $styleBorder = (new StyleBuilder())
            ->setBorder($border)
            ->build();
        /**
         * proses export
         */        
        $GLOBALS['synapse_export_isBreaking'] = false;
        $this->chunkWithLimit($data,100,$offset,$limit, function ($chunkedData) use(
            $cacheKey, 
            $isFirstRow,
            &$reader,
            &$writer,
            $startTime,
            $exportData,
            $border,
            $styleBorder,
            $tmpFilename
        ) {
            
            $chunkedData = $chunkedData->toArray();
            
            usleep(200);

            foreach ($chunkedData as $dataRow) {
                if(!empty($exportData['template']['coreMainLoopingMethod'])){
                    $exportData['template']['coreMainLoopingMethod'][0]::{$exportData['template']['coreMainLoopingMethod'][1]}(
                        $exportData,
                        $reader,
                        $writer,
                        $dataRow
                    );
                    continue;
                }

                // jika false berarti di cancel
                if($this->_checkAndCounter($cacheKey)==false){
                    return false;
                }

                $this->exportIncrementProcessedCount($cacheKey);                

                //jika tanpa template dan row 1 maka simpan nama2 kolomnya, untuk dijadikan header caption
                if($isFirstRow && empty($exportData['template']['filepath'])){                
                    $headerColumn = $this->formatExportExcelHeader($cacheKey,$dataRow);

                    //kolom terakhir header
                    // $countHeader = count($headerColumn);
                    $styleHeading = (new StyleBuilder())
                        ->setFontBold()
                        ->setBorder($border)
                        ->setBackgroundColor('CCCCCC')
                        ->build();

                    $writer->addRow(
                        WriterEntityFactory::createRowFromArray($headerColumn,$styleHeading)
                    );
                    
                    // if($exportData['driver']=='phpspreadsheet'){
                    //     $reader = Excel::setCell($reader, $headerColumn);
                    //     $reader = Excel::setBorder($reader,'A1:'.Excel::excol($countHeader).'1');
                    //     $reader = Excel::setFontBold($reader,'A1:'.Excel::excol($countHeader).'1');
                    //     $reader = Excel::setBackground($reader,'A1:'.Excel::excol($countHeader).'1','CCCCCC');
                    // }else{

                    // }
                    $isFirstRow = false;//tandai flag first row agar tidak masuk ke sini lg di row selanjutnya
                }

                // format record sesuai data yang diimport sekarang
                $insertRow = $this->formatExportExcelRow($cacheKey,$dataRow,$GLOBALS['synapse_export_indexExcelRow'],$GLOBALS['synapse_export_indexData']);

                if(!empty($exportData['template']['coreRowFormaterMethod'])){
                    $insertRow = $exportData['template']['coreRowFormaterMethod'][0]::{$exportData['template']['coreRowFormaterMethod'][1]}(
                        $exportData,$insertRow,$dataRow,$GLOBALS['synapse_export_indexExcelRow'],$GLOBALS['synapse_export_indexData']
                    );
                }

                // $reader = Excel::insertRow($reader, $GLOBALS['synapse_export_indexExcelRow'], $insertRow);

                $writer->addRow(
                    WriterEntityFactory::createRowFromArray($insertRow,$styleBorder)
                );

                $GLOBALS['synapse_export_indexExcelRow']++;
                $GLOBALS['synapse_export_indexData']++;                
            }
            
            //break proses setiap kurang dari setengah jam 
            // if((microtime(true)-$startTime)>=1800){
            if((microtime(true)-$startTime)>=5){
                $chunkedData = null;
                unset($chunkedData);
                $this->breakToNextExport($cacheKey, $tmpFilename, $reader, $writer, $GLOBALS['synapse_export_indexExcelRow'],$GLOBALS['synapse_export_indexData']);
                $GLOBALS['synapse_export_isBreaking'] = true;
                return false;
            }
            usleep(500);
        });

        $exportData = $this->getExport($cacheKey);
        if($exportData==false || $exportData['forceCancle']==1)return false;

        if($GLOBALS['synapse_export_isBreaking'])return true;

        // if($deleteRow) $reader->getActiveSheet()->removeRow($deleteRow);   
        
        $exportData = $this->getExport($cacheKey);
        $exportData['count'] = $GLOBALS['synapse_export_indexData']-1;        
        $this->updateExport($cacheKey,$exportData); 

        $this->appendExportLog($cacheKey,'<br>Save file to : '.$exportData['filename'].'<br>');

        $reader->close();
        $writer->close();

        // unlink($exportData['filepath']);
        rename($tmpFilename, $exportData['filepath']);
        
        if(!empty($exportData['template']['coreLastFormaterMethod'])){
            $exportData['template']['coreLastFormaterMethod'][0]::{$exportData['template']['coreLastFormaterMethod'][1]}(
                $exportData,$exportData['filepath']
            );
        }

        
        //pastikan semua selesai dan memory di-free-kan kembali
        $reader = null;
        $writer = null;
        $data = null;
        unset($reader,$data);

        //ubah status jadi ok
        $this->setExportDone($cacheKey);

        return true;        
    }
    
    private function chunkWithLimit ($model, $count,$offset=0,$remaining=null, callable $callback) 
    {
        do {
            if (! is_null($remaining)) {
                $limit = min($count, $remaining);
            } else {
                $limit = $count;
            }
            
            $results = $model->skip($offset)->take($limit)->get();

            $countResults = $results->count();

            if ($countResults == 0) {
                break;
            }

            // On each chunk result set, we will pass them to the callback and then let the
            // developer take care of everything within the callback, which allows us to
            // keep the memory low for spinning through large result sets for working.
            if (call_user_func($callback, $results) === false) {
                return false;
            }

            $offset += $countResults;

            if (! is_null($remaining)) {
                $remaining -= $countResults;
                if ($remaining == 0) {
                    break;
                }
            }
        } while ($countResults == $limit);

        return true;
    }   

    protected function formatExportExcelHeader($cacheKey,array $row1 = [])
    {
        $exportData = $this->getExport($cacheKey);
        $headerColumn=[];
        //jika ada format column maka gunakan format column
        if(!empty($exportData['template']['headerCaption'])){
            $i=0;
            foreach($exportData['template']['headerCaption'] as $format){ 
                $i++; 
                $headerColumn[] = empty($format[1]['caption'])?str_replace('_',' ',$format[0]):$format[1]['caption'];
            }
        }else{
            $i=0;
            foreach($row1 as $fieldName => $fieldValue){ 
                $i++; 
                $headerColumn[] = str_replace('_',' ',$fieldName);
            }

        }
        return $headerColumn;
    }

    /**
     * untuk nambah pemformatan setelah formating default dieksekusi
     * 
     * @param array $row array row database (dari model)
     * 
     * @return array
     */
    protected function formatExportExcelRow($cacheKey,array $row = [],int $indexExcelRow,int $indexData)
    {
        $exportData = $this->getExport($cacheKey);
        $insertRow=[];
        $i=0;
        //jika ada format column maka gunakan format column
        if(!empty($exportData['template']['headerCaption'])){
            foreach($exportData['template']['headerCaption'] as $format){
                $i++; 
                $insertRow[] = 
                    empty($row[$format[0]]) && isset($format[1]['default'])?
                    $format[1]['default']:
                    $this->exportFormatRowValue($row[$format[0]],$format[1]);
            }
        }else{
            foreach($row as $fieldValue){ 
                $i++; 
                $insertRow[] = is_array($fieldValue)?'':$fieldValue;
            }
        }

        return $insertRow;
    }

    /**
     * format value cell sesuai config format columnya
     * 
     * @param mixed value per cell/field dari database
     * @param array format, format dari konfig column, format :
     *  [
     *      'caption'=>'CAPTION COLUMNNYA',
     *      'default' => DEFAULT VALUE YANG DIGUNAKAN JIKA VALUE KOSONG ATAU JIKA TIDAK SESUAI FORMAT/TYPE
     *      'type' => 'type' ---> string, number, date, datetime, auto (default)
     *      'format' =>  ''--> format tambahan dari type, misal type date isi format 'Y-m-d'
     *  ]
     */
    protected function exportFormatRowValue($value,array $format = [])
    {
        if(isset($format['type'])){
            switch ($format['type']) {
                case 'string':
                    $value = (string) $value;
                    break;    
                case 'date':
                    $format['format'] = empty($format['format'])?'Y-m-d':$format['format'];
                    $value = (new Carbon($value))->format($format['format']);
                    break; 
                case 'datetime':
                    $format['format'] = empty($format['format'])?'Y-m-d h:m:s':$format['format'];
                    $value = (new Carbon($value))->format($format['format']);
                    break;  
                case 'integer': 
                case 'int':
                    $value = intval($value);
                    break; 
                case 'float':
                    $value = floatval($value);
                    break;             
                default:
                    # code...
                    break;
            }
        }

        return $value;

    }

    public function exportIncrementProcessedCount($cacheKey)
    {
        $exportData = $this->getExport($cacheKey);
        $exportData['processedCount']++;
        $this->updateExport($cacheKey,$exportData);
    }

    private function isExportJobsPerTenant($cacheKey)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        return config('AppConfig.system.jobs.multitenant_add',false) && !empty($exportData['tenantId']) && $exportData['tenantId']>0?true:false;
    }

    /**
     * saat jobs dipecah ke jobs selanjurnya
     */
    private function breakToNextExport($cacheKey,$tmpFilename,&$reader,&$writer,$lastExcelRow=1,$lastTableRow=1)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $this->appendExportLog(
            $cacheKey,
            '<br><span class="text-info">Break process to the next job, please wait</span>...<br>'
        );

        Excel::save($reader,$exportData['filepath']);

        
        $reader->close();
        $writer->close();

        // unlink($exportData['filepath']);
        rename($tmpFilename, $exportData['filepath']);

        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        unset($objWriter, $reader);

        $exportData['isResumeJob'] = true;
        $exportData['resumeJobParam']['lastExcelRow'] = $lastExcelRow;
        $exportData['resumeJobParam']['lastTableRow'] = $lastTableRow;
        $exportData['resumeJobParam']['jobDispatchTime'] = now()->format('Y-m-d H:i:s');
        $exportData['resumeJobParam']['jobStartTime'] = '';

        $this->updateExport($cacheKey,$exportData);
        $queueName = $this->nextExportQueue();

        if($this->isExportJobsPerTenant($cacheKey)){
            JExport::dispatch($cacheKey,'tenant'.$exportData['tenantId'].$queueName)->onQueue('tenant'.$exportData['tenantId'].$queueName);
        }else{
            JExport::dispatch($cacheKey,$queueName)->onQueue($queueName);;
        }
    }

}