<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;

use App\Facades\Excel;
use App\Facades\Tenant;

Use App\Jobs\Export as JExport;

use App\Base\BaseRepository;

class Export extends BaseRepository
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
        
        if($this->isExportJobsPerTenant($cacheKey)){
            JExport::dispatch($cacheKey)->onQueue('tenant'.$exportData['tenantId']);
        }else{
            JExport::dispatch($cacheKey);
        }

        return $exportData;
    }

    public function cancelExport($cacheKey)
    {

    }

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

        $exportData['cacheKey'] = '';

        $exportData['addsParam'] = [];
        $exportData['listingModel'] = '';//bisa array [class,static method]

        $exportData['listingParams'] = [];
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

        $exportData['inputTime'] = now()->format('Y-m-d H:i:s');//
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
    
    /**
     * proses utama yang dieksekusi dari jobs
     */
    public function processExport($cacheKey)
    {        
        ini_set('memory_limit','5524M');
        set_time_limit(0);
        
        $startTime = microtime(true);        

        /**
         * init status & var
         */
        $exportData = $this->getExport($cacheKey);
        $exportData['status'] = self::$EXPORT_STATUS_ON_PROGRESS;


        // jika resume dari jobs sebelumnya yang di split 
        if($exportData['isResumeJob']){

            $exportData['resumeJobParam']['jobStartTime'] = now()->format('Y-m-d H:i:s');
            $this->updateExport($cacheKey,$exportData);

            $this->appendExportLog($cacheKey,'<span class="text-info">Continueing process from previous jobs</span>...<br>');
            $this->appendExportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');

            $reader = Excel::load($exportData['filepath'], 'Xlsx', false);
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
                var_dump($exportData['listingParams']);
                $data = new $exportData['listingModel'];            
                $data = $this->_filter($data,$exportData['listingParams']['filter']);
            }            

            $exportData['count'] = $data->count();
            $exportData['jobStartTime'] = now()->format('Y-m-d H:i:s');
            
            $this->updateExport($cacheKey,$exportData); 

            $this->appendExportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');
            $this->appendExportLog($cacheKey,'Url will be at : '.$exportData['fileurl'].'<br>');
            $reader = Excel::load(
                $exportData['template']['filepath']?$exportData['template']['filepath']:resource_path('doc/generalExport.xlsx'), 
                'Xlsx',
                $exportData['template']['filepath']?false:true
            );

            $GLOBALS['synapse_export_indexExcelRow']=$exportData['template']['dataStartRow'];//urutan baris excel    
            $deleteRow=$GLOBALS['synapse_export_indexExcelRow'];//row yg harus didelete, kenapa didelete untuk memastikan style header tidak terbawa
            $GLOBALS['synapse_export_indexExcelRow']++;//start row ditambah satu agar style header tidak terbawa, karena nanti first row ini akan didelete juga
            $GLOBALS['synapse_export_indexData'] = 1;//nomor urut data dari 1 dst
            $isFirstRow = true;//flag untuk penanda baris pertama dari data
            $offset = 0;
            $limit = null;
        }        

        if(!is_array($exportData['listingModel']) && !empty($exportData['listingParams']['orderBy'])){

            if(!is_array($exportData['listingParams']['orderBy'][0]))
				$exportData['listingParams']['orderBy']=[$exportData['listingParams']['orderBy']];

            foreach($exportData['listingParams']['orderBy'] as $oBitem){
                $data = $data->orderBy($oBitem[0], $oBitem[1]);
            }
        }
        
        /**
         * proses export
         */
        
        $reader->setActiveSheetIndex(0);
        $GLOBALS['synapse_export_isBreaking'] = false;
        $this->chunkWithLimit($data,100,$offset,$limit, function ($chunkedData) use(
            $cacheKey, 
            $isFirstRow,
            &$reader,
            $startTime,
            $exportData
        ) {
            
            $chunkedData = $chunkedData->toArray();
            
            usleep(200);

            foreach ($chunkedData as $dataRow) {
                if(!empty($exportData['template']['coreMainLoopingMethod'])){
                    $exportData['template']['coreMainLoopingMethod'][0]::{$exportData['template']['coreMainLoopingMethod'][1]}(
                        $exportData,
                        $reader,
                        $dataRow
                    );
                    continue;
                }
                $this->appendExportLog($cacheKey,'. ');
                $this->exportIncrementProcessedCount($cacheKey);                

                //jika tanpa template dan row 1 maka simpan nama2 kolomnya, untuk dijadikan header caption
                if($isFirstRow && empty($exportData['template']['filepath'])){                
                    $headerColumn = $this->formatExportExcelHeader($cacheKey,$dataRow);

                    //kolom terakhir header
                    $countHeader = count($headerColumn);
                    $reader = Excel::setCell($reader, $headerColumn);
                    $reader = Excel::setBorder($reader,'A1:'.Excel::excol($countHeader).'1');
                    $reader = Excel::setFontBold($reader,'A1:'.Excel::excol($countHeader).'1');
                    $reader = Excel::setBackground($reader,'A1:'.Excel::excol($countHeader).'1','CCCCCC');
                    $isFirstRow = false;//tandai flag first row agar tidak masuk ke sini lg di row selanjutnya
                }

                // format record sesuai data yang diimport sekarang
                $insertRow = $this->formatExportExcelRow($cacheKey,$dataRow,$GLOBALS['synapse_export_indexExcelRow'],$GLOBALS['synapse_export_indexData']);

                if(!empty($exportData['template']['coreRowFormaterMethod'])){
                    $insertRow = $exportData['template']['coreRowFormaterMethod'][0]::{$exportData['template']['coreRowFormaterMethod'][1]}(
                        $exportData,$insertRow,$dataRow,$GLOBALS['synapse_export_indexExcelRow'],$GLOBALS['synapse_export_indexData']
                    );
                }

                $reader = Excel::insertRow($reader, $GLOBALS['synapse_export_indexExcelRow'], $insertRow);            
                $GLOBALS['synapse_export_indexExcelRow']++;
                $GLOBALS['synapse_export_indexData']++;                
            }
            
            //break proses setiap kurang dari setengah jam 
            if((microtime(true)-$startTime)>=1800){
                $chunkedData = null;
                unset($chunkedData);
                $this->breakToNextExport($cacheKey, $reader,$GLOBALS['synapse_export_indexExcelRow'],$GLOBALS['synapse_export_indexData']);
                $GLOBALS['synapse_export_isBreaking'] = true;
                return false;
            }
            usleep(500);
        });

        if($GLOBALS['synapse_export_isBreaking'])return true;

        if($deleteRow) $reader->getActiveSheet()->removeRow($deleteRow);   
        
        if(!empty($exportData['template']['coreLastFormaterMethod'])){
            $exportData['template']['coreLastFormaterMethod'][0]::{$exportData['template']['coreLastFormaterMethod'][1]}($exportData,$reader);
        }

        $exportData = $this->getExport($cacheKey);
        $exportData['count'] = $GLOBALS['synapse_export_indexData']-1;        
        $this->updateExport($cacheKey,$exportData); 

        $this->appendExportLog($cacheKey,'<br>Save file to : '.$exportData['filename'].'<br>');

        Excel::save($reader,$exportData['filepath']);

        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
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
                $headerColumn[Excel::excol($i).'1'] = empty($format[1]['caption'])?str_replace('_',' ',$format[0]):$format[1]['caption'];
            }
        }else{
            $i=0;
            foreach($row1 as $fieldName => $fieldValue){ 
                $i++; 
                $headerColumn[Excel::excol($i).'1'] = str_replace('_',' ',$fieldName);
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
                $insertRow[Excel::excol($i)] = 
                    empty($row[$format[0]]) && isset($format[1]['default'])?
                    $format[1]['default']:
                    $this->exportFormatRowValue($row[$format[0]],$format[1]);
            }
        }else{
            foreach($row as $fieldValue){ 
                $i++; 
                $insertRow[Excel::excol($i)] = is_array($fieldValue)?'':$fieldValue;
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
    private function breakToNextExport($cacheKey,&$reader,$lastExcelRow=1,$lastTableRow=1)
    {
        $exportData = $this->getExport($cacheKey);
        if($exportData==false)return false;

        $this->appendExportLog(
            $cacheKey,
            '<br><span class="text-info">Break process to the next job, please wait</span>...<br>'
        );

        Excel::save($reader,$exportData['filepath']);

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

        if($this->isExportJobsPerTenant($cacheKey)){
            JExport::dispatch($cacheKey)->onQueue('tenant'.$exportData['tenantId']);
        }else{
            JExport::dispatch($cacheKey);
        }
    }

}