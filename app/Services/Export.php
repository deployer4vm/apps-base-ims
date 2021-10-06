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
    private $_exportUploadPath = '/upload/export/';//default path ke upload relative dari public_path
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
        // $this->_saveCache(
        //     $this->_mainCacheKeyGroup,
        //     $this->_mainCacheKeyList,
        //     []
        // );
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
            $exportDetail = $this->getExport($cacheKey);
            if($exportDetail==false)$exportDetail = $this->initExportStatus();
            
            // dd($exportDetail);

            // jika sedang dalam proses
            if(
                $exportDetail['status']==self::$EXPORT_STATUS_DISPATCHED || 
                $exportDetail['status']==self::$EXPORT_STATUS_ON_PROGRESS
            ){
                $this->error = 'Jobs already exists';
                return false;
            }
        }

        // set baru export
        $exportDetail = $this->initExportStatus();
        $exportDetail['listingModel'] = $listingModel;
        $exportDetail['listingParams'] = $listingParams;
        $exportDetail['userId'] = $userId;
        $exportDetail['tenantId'] = $tenantId;

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
            $exportDetail
        );

        return $exportDetail;
    }

        
    /**
     * dispatch export ke queue untuk pertama kali
     * dieksekusi setelah createExport dan set-set config
     */
    public function dispatchExport($cacheKey)
    {
        $export = $this->getExport($cacheKey);
        if($export==false)return false;
        
        $export['laravelPath'] = $this->_exportUploadPath.$export['userId'].'/';
        $export['path'] = public_path($export['laravelPath']);
        // pastikan folder tujuan ada
        if(!file_exists($export['path'])){
            mkdir($export['path'],0777,true);
        }

        $export['jobDispatchTime'] = now()->format('Y-m-d H:i:s');
        $export['status'] = self::$EXPORT_STATUS_DISPATCHED;
        
        if(empty($export['filename']))
            $export['filename'] = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_',$this->_defaultFilename.'_'.$export['inputTime'])).'.xlsx';

        $export['laravelFilepath'] = $export['laravelPath'].$export['filename']; 
        $export['filepath'] = $export['path'].$export['filename'];   

        $export['fileurl'] = url($export['laravelFilepath']);
        
        // jika sudah ada maka rename
        $i=1;
        while (file_exists($export['filepath'])) {
            $export['filepath'] = $export['path'].$i.'_'.$export['filename'];
            $export['laravelFilepath'] = $export['laravelPath'].$i.'_'.$export['filename'];
            $i++;
        }

        $this->updateExport($cacheKey,$export);
        
        if($this->isExportJobsPerTenant($cacheKey)){
            JExport::dispatch($cacheKey)->onQueue('tenant'.$export['tenantId']);
        }else{
            JExport::dispatch($cacheKey);
        }

        return $export;
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
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        $export['filename'] = $filename;

        $this->updateExport($cacheKey,$export);

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
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        $export['template']['headerCaption'] = $columnCaption;
        $export['template']['headerCaptionRow'] = $headerRow;
        
        $this->updateExport($cacheKey,$export);
        return true;
    }

    /**
     * set template excel yang digunnakan (jika ingin menggunakan custom template)
     */
    public function setTemplate($cacheKey,$templatePath, $dataStartRow = 2)
    {
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        $export['template']['filepath'] = $templatePath;
        $export['template']['dataStartRow'] = $dataStartRow;
        
        $this->updateExport($cacheKey,$export);
        return true;
    }

    
    /**
     * set class dan method untuk memformat data per row
     */
    public function setDataFormater($cacheKey, string $dataFormaterClass, string $dataFormaterMethod)
    {
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        $export['template']['dataFormaterClass'] = $dataFormaterClass;
        $export['template']['dataFormaterMethod'] = $dataFormaterMethod;
        
        $this->updateExport($cacheKey,$export);
        return true;
    }
    
    /**
     * set class dan method untuk memformat excel $reader saat setelah beres semua
     */
    public function setLastFormater($cacheKey, string $lastFormaterClass, string $lastFormaterMethod)
    {
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        $export['template']['lastFormaterClass'] = $lastFormaterClass;
        $export['template']['lastFormaterMethod'] = $lastFormaterMethod;
        
        $this->updateExport($cacheKey,$export);
        return true;
    }
    /**
     * CORE - TIDAK DIAKSES / DIGUNAKAN DARI APLIKASI SECARA LANGSUNG
     * -------------------------------------------------------------------------
     */

    protected function initExportStatus()
    {  
        $config = [];

        $config['listingModel'] = '';

        $config['listingParams'] = [];
        $config['userId'] = 0;
        $config['tenantId'] = 0;

        $config['log'] = '';//log status

        $config['template'] = [
			//full template file export nya jika custom, kosong jika menggunakan default template
            'filepath'=>'',
			// format header caption khusus default template
            'headerCaption'=>[],
            'headerCaptionRow'=>1,
			// format data
            'dataStartRow'=>2,//baris pertama data diinsert
			
			'dataFormaterClass'=>'',// fullpath class ke formater
			'dataFormaterMethod'=>'',// static method nya
			
			'lastFormaterClass'=>'',// fullpath class ke formater
			'lastFormaterMethod'=>'',// static method nya
        ];

        $config['filename'] = '';//nama file export nya
		
        $config['path'] = '';// fullpath folder ke tempate file export berada
        $config['filepath'] = '';// $config['path'].'/'.$config['filename']
		
        $config['laravelPath'] = '';// path format laravel (yg bisa digunakan ke storage ke folder ke tempat file export berada
        $config['laravelFilepath'] = '';// $config['path'].'/'.$config['filename']
		
        $config['fileurl'] = '';//full url exportnya

        $config['count'] = 0;//jumlah total record yang harus diproses
        $config['processedCount'] = 0;//jumlah record yg sudah diproses

        $config['inputTime'] = now()->format('Y-m-d H:i:s');//
        $config['jobDispatchTime'] = '';//waktu pertama kali jobs diproses (saat ke status 1)
        $config['jobStartTime'] = '';//waktu jobs pertama pertama kali diproses (saat ke status 2)
        
        $config['isResumeJob'] = false;
        $config['resumeJobParam'] = [
            'jobDispatchTime' => '',
            'jobStartTime' => ''
        ];

        $config['status'] = 0;
        // status :
        // 0 new process
        // 1 sudah diinput ke jobs
        // 2 jobs sudah di dispatch (run) / sedang berjalan
        // 3 jobs selesai
        // 4 jobs gagal
        return $config;
    }

    protected function updateExport($cacheKey,$config) 
    {   
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey,
            $config
        );
    }

    protected function setExportDone($cacheKey)
    {
        $config = $this->getExport($cacheKey); 
        if($config==false)return false;

        $config['log'] .= '<br><b class="text-success">Export Done !</b><br>';
        $config['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $config['status'] = self::$EXPORT_STATUS_SUCCESS;//2: success
        $this->updateExport($cacheKey,$config); 
    }

    protected function setExportFailed($cacheKey)
    {
        $config = $this->getExport($cacheKey); 
        if($config==false)return false;

        $config['log'] .= '<br><b class="text-danger">Export Failed !</b><br>';
        $config['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $config['filename'] = '';
        $config['fileurl'] = '';
        $config['status'] = self::$EXPORT_STATUS_FAILED;//4: failed

        $this->updateExport($cacheKey,$config); 
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

    protected function appendExportLog($cacheKey,string $log='')
    {       
        $config = $this->getExport($cacheKey);
        if($config==false)return false;

        $config['log'] .= $log;
        $this->updateExport($cacheKey,$config);
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
        $config = $this->getExport($cacheKey);
        $config['status'] = self::$EXPORT_STATUS_ON_PROGRESS;


        // jika resume dari jobs sebelumnya yang di split 
        if($config['isResumeJob']){

            $config['resumeJobParam']['jobStartTime'] = now()->format('Y-m-d H:i:s');
            $this->updateExport($cacheKey,$config);

            $this->appendExportLog($cacheKey,'<span class="text-info">Continueing process from previous jobs</span>...<br>');
            $this->appendExportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');

            $reader = Excel::load($config['filepath'], 'Xlsx', false);
            $data = new $config['listingModel'];
            $data = $this->_filter($data,$config['listingParams']['filter']);
                        
            $offset = $config['resumeJobParam']['lastTableRow'];
            $limit = $config['count']+1000;

            $deleteRow = $config['template']['dataStartRow'];//row yg harus didelete, kenapa didelete untuk memastikan style header tidak terbawa
            $row = $config['resumeJobParam']['lastExcelRow'];
            $firstRow = false;//flag untuk penanda baris pertama dari data
            $noUrut = $config['resumeJobParam']['lastTableRow'];
            
        //jika jobs pertama maka
        }else{
            $data = new $config['listingModel'];
            $data = $this->_filter($data,$config['listingParams']['filter']);

            // echo $data->toSql();

            $config['count'] = $data->count();
            $config['jobStartTime'] = now()->format('Y-m-d H:i:s');
            
            $this->updateExport($cacheKey,$config); 

            $this->appendExportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');
            $this->appendExportLog($cacheKey,'Url will be at : '.$config['fileurl'].'<br>');
            $reader = Excel::load(
                $config['template']['filepath']?$config['template']['filepath']:resource_path('doc/generalExport.xlsx'), 
                'Xlsx',
                $config['template']['filepath']?false:true
            );

            $row=$config['template']['dataStartRow'];            
            $deleteRow=$row;//row yg harus didelete, kenapa didelete untuk memastikan style header tidak terbawa
            $row++;//start row ditambah satu agar style header tidak terbawa, karena nanti first row ini akan didelete juga
            $noUrut = 0;
            $firstRow = true;//flag untuk penanda baris pertama dari data
            $offset = 0;
            $limit = null;
        }        
        
        /**
         * proses export
         */
        
        $reader->setActiveSheetIndex(0);
        $isBreaking = false;
        $this->chunkWithLimit($data,100,$offset,$limit, function ($chunkedData) use(
            $cacheKey, 
            &$firstRow,
            &$reader,
            &$row,
            &$noUrut,
            $startTime,
            &$isBreaking,
            $config
        ) {
            
            $chunkedData = $chunkedData->toArray();
            
            usleep(200);

            foreach ($chunkedData as $val) {
                $this->appendExportLog($cacheKey,'. ');
                $this->exportIncrementProcessedCount($cacheKey);                

                //jika tanpa template dan row 1 maka simpan nama2 kolomnya, untuk dijadikan header caption
                if($firstRow && empty($config['template']['filepath'])){                
                    $headerColumn = $this->formatExportExcelHeader($cacheKey,$val);

                    //kolom terakhir header
                    $countHeader = count($headerColumn);
                    $reader = Excel::setCell($reader, $headerColumn);
                    $reader = Excel::setBorder($reader,'A1:'.Excel::excol($countHeader).'1');
                    $reader = Excel::setFontBold($reader,'A1:'.Excel::excol($countHeader).'1');
                    $reader = Excel::setBackground($reader,'A1:'.Excel::excol($countHeader).'1','CCCCCC');
                    $firstRow = false;//tandai flag first row agar tidak masuk ke sini lg di row selanjutnya
                }

                // format record sesuai data yang diimport sekarang
                $insertRow = $this->formatExportExcelRow($cacheKey,$val,$row);

                if($config['template']['dataFormaterClass']){
                    $insertRow = $config['template']['dataFormaterClass']::{$config['template']['dataFormaterMethod']}(
                        $cacheKey,$insertRow,$val,$row
                    );
                }

                $reader = Excel::insertRow($reader, $row, $insertRow);            
                $row++;
                $noUrut++;                
            }
            
            //break proses setiap kurang dari setengah jam 
            if((microtime(true)-$startTime)>=1800){
                $chunkedData = null;
                unset($chunkedData);
                $this->breakToNextExport($cacheKey, $reader,$row,$noUrut);
                $isBreaking = true;
                return false;
            }
            usleep(500);
        });

        if($isBreaking)return true;

        if($deleteRow) $reader->getActiveSheet()->removeRow($deleteRow);   
        
        if($config['template']['lastFormaterClass']){
            $config['template']['lastFormaterClass']::{$config['template']['lastFormaterMethod']}($reader);
        }

        $this->appendExportLog($cacheKey,'<br>Save file to : '.$config['filename'].'<br>');

        Excel::save($reader,$config['filepath']);

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
        $config = $this->getExport($cacheKey);
        $headerColumn=[];
        //jika ada format column maka gunakan format column
        if(!empty($config['template']['headerCaption'])){
            $i=0;
            foreach($config['template']['headerCaption'] as $format){ 
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
    protected function formatExportExcelRow($cacheKey,array $row = [],int $rowNumber)
    {
        $config = $this->getExport($cacheKey);
        $insertRow=[];
        $i=0;
        //jika ada format column maka gunakan format column
        if(!empty($config['template']['headerCaption'])){
            foreach($config['template']['headerCaption'] as $format){
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

    protected function exportIncrementProcessedCount($cacheKey)
    {
        $config = $this->getExport($cacheKey);
        $config['processedCount']++;
        $this->updateExport($cacheKey,$config);
    }

    private function isExportJobsPerTenant($cacheKey)
    {
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        return config('AppConfig.system.jobs.multitenant_add',false) && !empty($export['tenantId']) && $export['tenantId']>0?true:false;
    }

    /**
     * saat jobs dipecah ke jobs selanjurnya
     */
    private function breakToNextExport($cacheKey,&$reader,$lastExcelRow=1,$lastTableRow=1)
    {
        $export = $this->getExport($cacheKey);
        if($export==false)return false;

        $this->appendExportLog(
            $cacheKey,
            '<br><span class="text-info">Break process to the next job, please wait</span>...<br>'
        );

        Excel::save($reader,$export['filepath']);

        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        unset($objWriter, $reader);

        $export['isResumeJob'] = true;
        $export['resumeJobParam']['lastExcelRow'] = $lastExcelRow;
        $export['resumeJobParam']['lastTableRow'] = $lastTableRow;
        $config['resumeJobParam']['jobDispatchTime'] = now()->format('Y-m-d H:i:s');
        $config['resumeJobParam']['jobStartTime'] = '';

        $this->updateExport($cacheKey,$export);

        if($this->isExportJobsPerTenant($cacheKey)){
            JExport::dispatch($cacheKey)->onQueue('tenant'.$export['tenantId']);
        }else{
            JExport::dispatch($cacheKey);
        }
    }

}