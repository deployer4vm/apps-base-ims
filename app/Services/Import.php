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

Use App\Jobs\Import as JImport;
use Illuminate\Support\Facades\Schema;

class Import extends BaseRepository
{   
    protected $cacheActive = true;

    // 0 new process
    // 1 sudah diinput/dispatch ke jobs
    // 2 jobs sudah / sedang berjalan
    // 3 jobs selesai
    // 4 jobs gagal
    static $IMPORT_STATUS_NEW = 0;
    static $IMPORT_STATUS_DISPATCHED = 1;
    static $IMPORT_STATUS_ON_PROGRESS = 2;
    static $IMPORT_STATUS_SUCCESS = 3;
    static $IMPORT_STATUS_FAILED = 4;

    //
    protected $_mainCacheKeyGroup = 'synapse.import';//
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
     * list seluruh import
     */
    public function listImport()
    {
        $list = $this->_getCache($this->_mainCacheKeyGroup,$this->_mainCacheKeyList,[]);
        return $list;
    }
    
    public function nextImportQueue()
    {        
        $idxQueue=1;
        while (Job::where('queue','import'.$idxQueue)->exists()) {
            $idxQueue++;
            // berarti semua penuh, maka tambahkan ke yg paling sedikit
            if($idxQueue>5){
                $ret = DB::select("SELECT * FROM (SELECT COUNT(*) AS 'jm_queue',`queue` FROM `jobs` WHERE `queue` LIKE 'import%' GROUP BY `queue`) AS tmp_table ORDER BY `jm_queue` ASC LIMIT 1");
                return isset($ret['queue'])?$ret['queue']:'import1';
            }
        }
        return 'import'.$idxQueue;
    }

    /**
     * get data import
     */
    public function getImport($cacheKey) 
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
     * STEP 1 - yang harus diset pertama kali di controllernya (atau di tempat import ini dicreate/diinisasi)
     * 
     * membuat process import baru
     * 
     * @param String $cacheKey
     * @param String $importModel
     * @param String $filepath     full path excel yg diimport
     * @param Integer $dataStartRow
     */
    public function createImport($cacheKey,$importModel,$filepath,$dataStartRow,$userId=0,$tenantId=0)
    {
        $tenantId = $tenantId?$tenantId:config('tenant.id',0);
        
        $importList = $this->_getCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyList,
            []
        );

        // jika belum ada maka create
        if(!isset($importList[$cacheKey])){
            $importList[$cacheKey] = $cacheKey;

        // jika sudah ada pastikan tidak dalam proses
        }else{
            $importData= $this->getImport($cacheKey);
            if($importData==false)$importData= $this->initImportStatus();
            
            // jika sedang dalam proses
            if(
                $importData['status']==self::$IMPORT_STATUS_DISPATCHED || 
                $importData['status']==self::$IMPORT_STATUS_ON_PROGRESS
            ){
                $this->error = 'Jobs already exists';
                return false;
            // delete file sebelumnya
            }else{
                Storage::delete($importData['filepath']);
            }
        }

        // set baru import
        $importData= $this->initImportStatus();
        $importData['cacheKey'] = $cacheKey;
        $importData['importModel'] = $importModel;
        $importData['filepath'] = $filepath;
        $importData['format']['dataStartRow'] = $dataStartRow;
        $importData['userId'] = $userId;
        $importData['tenantId'] = $tenantId;

        // tambahkan ke list
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyList,
            $importList
        );

        // tambah detail nya
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey,
            $importData
        );

        return $importData;
    }

        
    /**
     * dispatch import ke queue untuk pertama kali
     * dieksekusi setelah createImport dan set-set config
     */
    public function dispatchImport($cacheKey)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;
        
        $importData['jobDispatchTime'] = now()->format('Y-m-d H:i:s');
        $importData['status'] = self::$IMPORT_STATUS_DISPATCHED;
        
        $this->updateImport($cacheKey,$importData);
        $queueName = $this->nextImportQueue();

        if($this->isImportJobsPerTenant($cacheKey)){
            JImport::dispatch($cacheKey,'tenant'.$importData['tenantId'].$queueName)->onQueue('tenant'.$importData['tenantId'].$queueName);
        }else{
            JImport::dispatch($cacheKey,$queueName)->onQueue($queueName);;
        }

        return $importData;
    }

    /**
     * cancel import yg sedang berjalan
     */
    public function cancelImport($cacheKey)
    {
        $importData = $this->getImport($cacheKey); 
        if($importData==false)return false;
        $importData['forceCancle'] = 1;//tandai sebagai force cancle
        $this->updateImport($cacheKey,$importData);

        return true;
    }

    /**
     * delete log import dan file hasil importnya
     */
    public function deleteImport($cacheKey)
    {
        $this->_deleteCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey
        );
        return true;

    }

    /**
     * FUNGSI CONFIG SETER SETELAH createImport
     * -------------------------------------------------------------------------
     */


    /**
     * set general parameter yang bisa digunakan untuk custom formating nantinya
     */
    public function setAddsParam($cacheKey,$addsParam)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        $importData['addsParam'] = $addsParam;
        
        $this->updateImport($cacheKey,$importData);
        return true;
    }

    /**
     * OVERIDE MAIN PROCESS
     * method-method untuk mengganti method utama dalam pemrosesan data
     */

    public function setCoreMainLooping($cacheKey, string $coreMainLoopingClass, string $coreMainLoopingMethod)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        $importData['format']['coreMainLoopingMethod'] = [$coreMainLoopingClass,$coreMainLoopingMethod];
        
        $this->updateImport($cacheKey,$importData);
        return true;
    }
    
    /**
     * set class dan method untuk memformat data per row
     */
    public function setCoreRowFormater($cacheKey, string $coreRowFormaterClass, string $coreRowFormaterMethod)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        $importData['format']['coreRowFormaterMethod'] = [$coreRowFormaterClass,$coreRowFormaterMethod];
        
        $this->updateImport($cacheKey,$importData);
        return true;
    }
    
    /**
     * set class dan method untuk memformat excel $reader saat setelah beres semua
     */
    public function setCoreLastFormater($cacheKey, string $coreLastFormaterClass, string $coreLastFormaterMethod)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        $importData['format']['coreLastFormaterMethod'] = [$coreLastFormaterClass,$coreLastFormaterMethod];
        
        $this->updateImport($cacheKey,$importData);
        return true;
    }

    /**
     * CORE - TIDAK DIAKSES / DIGUNAKAN DARI APLIKASI SECARA LANGSUNG
     * -------------------------------------------------------------------------
     */

    protected function initImportStatus()
    {  
        $importData = [];

        $importData['jobsId'] = 0;//id table jobs
        $importData['processId'] = 0;//id process
        $importData['forceCancle'] = 0;//1 jika force cancel

        $importData['cacheKey'] = '';
        $importData['queue'] = '';

        $importData['addsParam'] = [];//general additional parameter jika diperlukan
        $importData['importModel'] = '';//bisa array [class,static method]

        $importData['userId'] = 0;
        $importData['tenantId'] = 0;

        $importData['log'] = '';//log status

        $importData['format'] = [
            'formatRow'=>[],// format per index kolom (dari 0 dst)
            'dataStartRow'=>2,//baris pertama data diget
            'coreMainLoopingMethod' => [],// [class,static method]			
			'coreRowFormaterMethod'=>[],// [class,static method]			
			'coreLastFormaterMethod'=>[],// [class,static method]
        ];

        $importData['filepath'] = '';// file path excel yang diupload
				
        $importData['processedCount'] = 0;//jumlah record yg sudah diproses

        $importData['inputTime'] = now()->format('Y-m-d H:i:s');//waktu import dicreate pertama kali
        $importData['jobDispatchTime'] = '';//waktu pertama kali jobs diproses (saat ke status 1)
        $importData['jobStartTime'] = '';//waktu jobs pertama pertama kali diproses (saat ke status 2)
        
        $importData['status'] = 0;
        // status :
        // 0 new process
        // 1 sudah diinput ke jobs
        // 2 jobs sudah di dispatch (run) / sedang berjalan
        // 3 jobs selesai
        // 4 jobs gagal
        return $importData;
    }

    protected function updateImport($cacheKey,$importData) 
    {   
        $this->_saveCache(
            $this->_mainCacheKeyGroup,
            $this->_mainCacheKeyDetailPrefix.$cacheKey,
            $importData
        );
    }

    protected function setImportDone($cacheKey)
    {
        $importData = $this->getImport($cacheKey); 
        if($importData==false)return false;

        $importData['log'] .= '<br><b class="text-success">Import Done !</b><br>';
        $importData['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $importData['status'] = self::$IMPORT_STATUS_SUCCESS;//2: success
        $this->updateImport($cacheKey,$importData); 
    }

    /**
     * diexekusi saat import gagal
     */
    protected function setImportFailed($cacheKey)
    {
        $importData = $this->getImport($cacheKey); 
        if($importData==false)return false;

        $importData['log'] .= '<br><b class="text-danger">Import Failed !</b><br>';
        $importData['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $importData['status'] = self::$IMPORT_STATUS_FAILED;//4: failed

        $this->updateImport($cacheKey,$importData); 
    }
    
    /**
     * set dari cronjob, jika cronjob ada uncaught error
     * 
     * @param Exception $exception instance Exception dari job failed
     */
    public function setImportJobFailed($cacheKey,Exception $exception)
    {
        $this->setImportFailed($cacheKey); 

        $log = "<br><b class='text-danger'>Jobs terminated !</b>\n<hr>\n\nError message :<br>\n";
        $log .= $exception->getMessage();
        $log .= '<hr>';
        $log .= str_replace("\n",'<br>', $exception->getTraceAsString());

        $this->appendImportLog($log);
        report($exception); //lanjutkan error ke login (meureun)
    }

    public function appendImportLog($cacheKey,string $log='')
    {       
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        $importData['log'] .= $log;
        $this->updateImport($cacheKey,$importData);
    }

    /**
     * -------------------------------------------------------------------------
     */
    
    private function _initImportData($importData,$curQueue)
    {
        // set teknikal
        $importData['queue'] = $curQueue;
        $importData['processId'] = getmypid();

        $jobs = Job::where('payload','LIKE','%\"'.$importData['cacheKey'].'\\\\\"%')->get()->append(['formated_payload'])->toArray();
        foreach ($jobs as $value) {
            $importData['jobsId'] = $value['id'];
            break;
        }

        return $importData;
    }


    private function _checkAndCounter($cacheKey)
    {       
        $importData = $this->getImport($cacheKey);
        if($importData==false)
            return false;
            
        if($importData['forceCancle']==1){            
            $importData['log'] .= '<br><b class="text-danger">Import Canceled !</b><br>';
            $importData['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
            $importData['status'] = self::$IMPORT_STATUS_FAILED;//4: failed
            $this->updateImport($cacheKey,$importData);
            return false;
        }

        $importData['log'] .= '. ';
        $importData['processedCount']++;
        $this->updateImport($cacheKey,$importData);
        return true;
    }
    
    public function iimportIncrementProcessedCount($cacheKey)
    {
        $importData = $this->getImport($cacheKey);
        $importData['processedCount']++;
        $importData['log'] .= '. ';
        $this->updateImport($cacheKey,$importData);
    }
    /**
     * proses utama yang dieksekusi dari jobs
     */
    public function processImport($cacheKey,$curQueue='import1')
    {        
        ini_set('memory_limit','5524M');
        set_time_limit(0);
        
        /**
         * init status & var
         */
        $importData = $this->getImport($cacheKey);
        $importData['status'] = self::$IMPORT_STATUS_ON_PROGRESS;
        $importData = $this->_initImportData($importData,$curQueue);       

        $importData['jobStartTime'] = now()->format('Y-m-d H:i:s');
        
        $this->updateImport($cacheKey,$importData); 

        $this->appendImportLog($cacheKey,'<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');
        
        // we need a reader to read the existing file...
        $reader = ReaderEntityFactory::createReaderFromFile($importData['filepath']);
        $reader->setShouldFormatDates(true); // this is to be able to copy dates
        $reader->open($importData['filepath']);

        $idxRow = 1;//nomor urut data dari 1 dst
        $i=1;
        
        // pastikan format data nya sudah ada, jika belum ada maka generate default
        if(empty($importData['format']['formatRow']) || empty($importData['format']['coreRowFormaterMethod']))
            $importData['format']['formatRow'] = $this->formatImportDefault($importData);

        // let's read the entire spreadsheet...
        foreach ($reader->getSheetIterator() as $sheetIndex => $sheet) {
            foreach ($sheet->getRowIterator() as $row) {

                if($i<$importData['format']['dataStartRow']){
                    $i++;
                    continue;
                }

                $dataRow = $row->getCells();

                // jika false berarti di cancel
                // if($this->_checkAndCounter($cacheKey)==false)
                //     return false;

                if(!empty($importData['format']['coreMainLoopingMethod'])){
                    $importData['format']['coreMainLoopingMethod'][0]::{$importData['format']['coreMainLoopingMethod'][1]}(
                        $this,
                        $importData,
                        $dataRow,
                        $idxRow
                    );
                    continue;
                }
                        
                if(empty($importData['format']['coreRowFormaterMethod'])){
                    $insertRow = $this->formatImportExcelRow($importData,$dataRow,$idxRow);
                }else{
                    $insertRow = $importData['format']['coreRowFormaterMethod'][0]::{$importData['format']['coreRowFormaterMethod'][1]}(
                        $importData,
                        $dataRow,
                        $idxRow
                    );
                }
                
                // default insert data
                $importData['importModel']::insert($insertRow);

                $this->iimportIncrementProcessedCount($cacheKey);
                $idxRow++;
            }
        }

        $reader->close();
        //pastikan semua selesai dan memory di-free-kan kembali
        $reader = null;
        unset($reader);
        
        if(!empty($importData['format']['coreLastFormaterMethod'])){
            $importData['format']['coreLastFormaterMethod'][0]::{$importData['format']['coreLastFormaterMethod'][1]}(
                $this,$importData
            );
        }        

        //ubah status jadi ok
        $this->setImportDone($cacheKey);

        return true;        
    }
    
    /**
     * set format row
     * 
     * @param String $cacheKey
     * @param Array $formatRow isi kolom sesuai urutan dengan format :
     *      [
     *              [
     *                  'field' => NAMA FIELD,
     *                  'default' => ISI DEFAULT VALUE,
     *                  'type' => string tipe datanya,
     *                  'format' => format tambahan dari type, misal type date isi format 'Y-m-d'
     *              ]
     *              ,...
     *      ]
     */
    public function setFormatRow($cacheKey,array $formatRow)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        $importData['format']['formatRow'] = $formatRow;
        
        $this->updateImport($cacheKey,$importData);
        return true;
    }

    /**
     * untuk nambah pemformatan setelah formating default dieksekusi
     * 
     * @param array $row array row database (dari model)
     * 
     * @return array
     */
    protected function formatImportExcelRow($importData,array $row = [],int $indexData)
    {
        $insertRow=[];
        
        foreach($importData['format']['formatRow'] as $idx => $format){
            $insertRow[] = 
                empty($row[$idx]) && isset($format['default'])?
                $format['default']:
                $this->importFormatRowValue($row[$idx],$format);
        }

        return $insertRow;
    }

    /**
     * get nama field sesuai urutan di database
     */
    protected function formatImportDefault($importData)
    {
        $model = new $importData['importModel']();
        if($model->getConnectionName()){
            $fields = Schema::connection($model->getConnectionName())->getColumnListing($model->getTable());
        }else{
            $fields = Schema::getColumnListing($model->getTable());
        }
        $fieldFormated = [];
        foreach ($fields as $key => $value) {
            $fieldFormated[] = [
                'field' => $value
            ];
        }
        return $fieldFormated;
    }
    

    /**
     * format value cell sesuai config format columnya
     * 
     * @param mixed value per cell/field dari database
     * @param array format, format dari konfig column, format :
     *  [
     *      'field' => NAMA FIELD,
     *      'default' => DEFAULT VALUE YANG DIGUNAKAN JIKA VALUE KOSONG ATAU JIKA TIDAK SESUAI FORMAT/TYPE
     *      'type' => 'type' ---> string, number, date, datetime, auto (default)
     *      'format' =>  ''--> format tambahan dari type, misal type date isi format 'Y-m-d'
     *  ]
     */
    protected function importFormatRowValue($value,array $format = [])
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
    private function isImportJobsPerTenant($cacheKey)
    {
        $importData = $this->getImport($cacheKey);
        if($importData==false)return false;

        return config('AppConfig.system.jobs.multitenant_add',false) && !empty($importData['tenantId']) && $importData['tenantId']>0?true:false;
    }

}