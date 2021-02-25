<?php
namespace App\Base\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use App\Facades\Excel;
use App\Jobs\ResExport;
use App\Facades\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Excel Export/download Trait - fungsi-fungsi untuk handling download
 */
trait ResExportTrait { 

    // use ResCacheTrait;
    
    static $EXPORT_STATUS_READY = 0;//sedang tidak ada proses export
    static $EXPORT_STATUS_ON_PROGRESS = 1;//export sedang dalam proses
    static $EXPORT_STATUS_SUCCESS = 2;//export berhasil
    static $EXPORT_STATUS_FAILED = 3;//export gagal
    
    private $_exportFunctionInitialize = false;
    
    private $_exportGroup = '';
    private $_exportTemplate = '';//file template sebagai base export nya, string kosong maka auto generate
    private $_exportTemplateMainAppDoc = true;//apakah file template yg diinput dari mainapp doc
    private $_exportTemplateStartRow = 2;//start data mulai diinsert
    private $_exportModel = null;//instansi model builder yg diexport
    private $_exportAddsJobsParam = [];//parameter tambahan ke jobs parameter

    private $_exportUseJobs = true;//sementara belum ada opsi pake jobs atau tidak, HARUS pake jobs
    private $_exportHomeUrl = '';
    private $_exportUploadPath = '/upload/export/';//path ke upload relative dari public_path

    private $_exportColumn = [];//daftar field yang diexport, jika array kosong maka semua field diexport
    private $_resumeParams = [];

    private $_tenantId = 0;

    /**
     * fungsi utama yang harus dieksekusi untuk menggunakan trait ini
     */
    public function initExport(string $exportGroup = '', $model=null, array $addsJobsParam=[], string $template = '')
    {
        $this->_exportGroup = $exportGroup; 

        $this->setExportModel($model);
        $this->setExportTemplate($template);
        $this->setExportJobsParam($addsJobsParam);
        $this->setExportHomeUrl(url(''));

        $this->_exportFunctionInitialize = true; 
    }

    /**
     * set template
     * -----
     */
    public function getExportTemplate()
    {
        return $this->_exportTemplate;
    }
    public function getExportTemplateStartRow()
    {
        return $this->_exportTemplateStartRow;
    }

    public function setExportTemplate(string $template = '',int $startRow=0, bool $mainAppDoc = true)
    {
        if($template)$this->_exportTemplate = $template;
        if($mainAppDoc)$this->_exportTemplateMainAppDoc = $mainAppDoc;
        if($startRow)$this->_exportTemplateStartRow = $startRow;
    }

    /**
     * jobs param
     * -----
     */
    public function getExportJobsParam()
    {
        return $this->_exportAddsJobsParam;
    }

    public function setExportJobsParam(array $addsJobsParam = [])
    {
        $this->_exportAddsJobsParam = $addsJobsParam;
    }

    /**
     * export model
     * -----
     */
    public function getExportModel()
    {
        return $this->_exportModel;
    }

    public function setExportModel($model)
    {
        $this->_exportModel = $model;
    }

    /**
     * export column, list field yg di-export
     * -----
     */
    public function getExportColumn()
    {
        return $this->_exportColumn;
    }

    /**
     * @param array $exportColumn format : 
     *  [
     *      ['field_name'=>
     *          [
     *              'caption'=>'CAPTION COLUMNNYA',
     *              'default' => DEFAULT VALUE YANG DIGUNAKAN JIKA VALUE KOSONG ATAU JIKA TIDAK SESUAI FORMAT/TYPE
     *              'type' => 'type' ---> string, number, date, auto (default)
     *              'format' =>  --> format tambahan dari type, misal type date isi format 'Y-m-d'
     *          ]
     *      ],
     *      [..field selanjutny]
     *  ]
     */
    public function setExportColumn(array $exportColumn = [])
    {
        $this->_exportColumn = $exportColumn;
    }
    /**
     * export home url
     * -----
     */
    public function getExportHomeUrl()
    {
        return $this->_exportHomeUrl;
    }
    
    public function setExportHomeUrl(string $homeUrl='')
    {
        $this->_exportHomeUrl = $homeUrl;
    }
    
    /**
     * export upload path
     * -----
     */
    public function getExportUploadPath()
    {
        return $this->_exportUploadPath;
    }

    protected function setExportUploadPath(string $uploadPath='')
    {
        $this->_exportUploadPath = $uploadPath;
    }
    
    public final function setExportTenantId($tenantId=0)
    {
        $this->_tenantId = $tenantId;

        // jika dijobs dan pertenant tapi tenant nya ga ke detek maka set tenant
        // if($tenantId!=0 && config('tenant.id',0)==0){
        //     Tenant::setActiveTenantById($tenantId);
        // }
    }
    
    /**
     * OVERRIDEABLE
     * fungsi untuk di overide di parent repo yg menggunakan export trait ini (jika diperlukan)
     * 
     * method ini di eksekusi di job
     */
    public function initExportOnJob(array $addsJobsParam=[])
    {
        //jika ternyata tidak mengoverload method ini,
        //maka saat method ini dieksekusi di job, cek apakah initExport sudah diproses, jika belum maka eksekusi
        if(!$this->_exportFunctionInitialize){
            $this->initExport($this->_exportGroup,$this->_exportModel, $addsJobsParam, $this->_exportTemplate);
            $this->_exportFunctionInitialize = true; 
        }              
    }

    /**
     * Start export process, fungsi yang dieksekusi pertama kali dari controller
     * atau tempat lain untuk mentrigger export
     */
    public function startExport(array $addsJobsParam = [])
    {
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        //jika sedang ada proses export
        if(!$this->isExportReady()){
            return $this->getExportStatus();
        }

        $this->setExportTenantId(config('tenant.id'));
        if(!empty($addsJobsParam))$this->setExportJobsParam($addsJobsParam);

        $this->setExportStartProcess();  
        $config =  $this->getExportStatus();   

        if($this->isExportJobsPerTenant()){
            ResExport::dispatch(
                self::class,
                $this->getExportJobsParam(),
                url('')
            )->onQueue('tenant'.$this->_tenantId);
        }else{
            ResExport::dispatch(
                self::class,
                $this->getExportJobsParam(),
                url('')
            );
        }

        return $config;
    }

    public function isExportJobsPerTenant()
    {
        return config('AppConfig.system.jobs.multitenant_add',false) && !empty($this->perTenant) && $this->_tenantId>0?true:false;
    }

    /**
     * set params import, sebagai penanda bahwa jobs ini adalah kelanjutan dari jobs sebelumnya
     * (jika si $resumeParams nya tidak kosong)
     */
    public function setExportAsResume(array $resumeParams = [])
    {
        $this->_resumeParams = $resumeParams;
        if(!empty($this->_resumeParams))$this->onExportResume();
    }

    public function getExportResumeParam()
    {
        return $this->_resumeParams;
    }

    /**
     * untuk diOVERRIDE
     * dieksekusi saat pertama kali export diresume
     */
    public function onExportResume()
    {

    }
    /**
     * proses compute export excel, di eksekusi dari jobs
     */
    public function processExport()
    {        
        ini_set('memory_limit','5524M');
        set_time_limit(0);
        
        $startTime = microtime(true);

        if(!$this->_exportFunctionInitialize){
            return false;
        }
        
        /**
         * init status & var
         */
        $config = $this->getExportStatus();

        //jika jobs pertama maka
        if(empty($this->_resumeParams)){
            $data = $this->_exportModel;//->get();
            $config['count'] = $data->count();
            $config['filename'] = strtoupper(preg_replace('/[^a-zA-Z0-9]+/', '_',$this->_exportGroup.'_'.$config['date'])).'.xlsx';
            $fileName = $this->_exportUploadPath.$config['filename'];
            $config['urlFilename'] = $this->_exportHomeUrl.$fileName;        
            $this->saveExportStatus($config); 

            $this->appendExportLog('<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');
            $this->appendExportLog('Url will be at : '.$config['urlFilename'].'<br>');
            $reader = Excel::load($this->_exportTemplate?$this->_exportTemplate:'generalExport.xlsx', 'Xlsx',$this->_exportTemplateMainAppDoc);

            $row=$this->getExportTemplateStartRow();
            $deleteRow=$row;//row yg harus didelete, kenapa didelete untuk memastikan style header tidak terbawa
            $row++;//start row ditambah satu agar style header tidak terbawa, karena nanti first row ini akan didelete juga
            $noUrut = 0;
            $firstRow = true;//flag untuk penanda baris pertama dari data
            $offset = 0;
            $limit = null;
            
        // jika resume dari jobs sebelumnya yang di split 
        }else{
            $this->appendExportLog('<span class="text-info">Continueing process from previous jobs</span>...<br>');
            $this->appendExportLog('<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span><br>');

            $fileName = $this->_exportUploadPath.$config['filename'];
            $reader = Excel::load(public_path($fileName), 'Xlsx', false);
            $data = $this->_exportModel;//->offset($this->_resumeParams['lastTableRow'])->limit($config['count']+1000);//->get();
            
            $offset = $this->_resumeParams['lastTableRow'];
            $limit = $config['count']+1000;

            $deleteRow=$this->getExportTemplateStartRow();
            $row = $this->_resumeParams['lastExcelRow'];
            $firstRow = false;//flag untuk penanda baris pertama dari data
            $noUrut = $this->_resumeParams['lastTableRow'];
        }        
         
        /**
         * proses export
         */
        
        $reader->setActiveSheetIndex(0);
        $isBreaking = false;
        $this->chunkWithLimit($data,100,$offset,$limit, function ($chunkedData) use(&$firstRow,&$reader,&$row,&$noUrut,$startTime,$fileName,&$isBreaking) {
            $chunkedData = $this->formatExportMainData($chunkedData->toArray());
            
            usleep(200);

            foreach ($chunkedData as $val) {
                $this->appendExportLog('. ');
                $this->exportIncrementProcessedCount();

                //jika tanpa template dan row 1 maka simpan nama2 kolomnya, untuk dijadikan header caption
                if($firstRow && empty($this->_exportTemplate)){                
                    $headerColumn = $this->formatExportExcelHeaderAfter(
                        $this->formatExportExcelHeader($val),
                        $val
                    );
                    //kolom terakhir header
                    $countHeader = count($headerColumn);
                    $reader = Excel::setCell($reader, $headerColumn);
                    $reader = Excel::setBorder($reader,'A1:'.Excel::excol($countHeader).'1');
                    $reader = Excel::setFontBold($reader,'A1:'.Excel::excol($countHeader).'1');
                    $reader = Excel::setBackground($reader,'A1:'.Excel::excol($countHeader).'1','CCCCCC');
                    $firstRow = false;//tandai flag first row agar tidak masuk ke sini lg di row selanjutnya
                }

                $insertRow = $this->formatExportExcelRowAfter(
                    $this->formatExportExcelRow($val,$row),
                    $val,
                    $row
                );

                $reader = Excel::insertRow($reader, $row, $insertRow);            
                $row++;
                $noUrut++;
                
            }
            
            //break proses setiap kurang dari setengah jam 
            if((microtime(true)-$startTime)>=1800){
                $chunkedData = null;
                unset($chunkedData);
                // $reader = $this->breakExcelReader($reader,$fileName,$row,$noUrut);
                $this->onBreakToNextExport();
                $this->breakToNextExport($reader,$fileName,$row,$noUrut);
                $isBreaking = true;
                return false;
            }
            usleep(500);
        });

        if($isBreaking)return true;

        if($deleteRow) $reader->getActiveSheet()->removeRow($deleteRow);        

        $this->appendExportLog('<br>Save file to : '.$fileName.'<br>');
        if(!file_exists(public_path($this->_exportUploadPath))){
            mkdir(public_path($this->_exportUploadPath),0777,true);
        }

        Excel::save($reader,public_path($fileName));

        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        $data = null;
        unset($reader,$data);

        //ubah status jadi ok
        $this->setExportDone();

        return true;        
    }

    private function chunkWithLimit ($model, $count,$offset=0,$remaining=null, callable $callback) {
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

    /**
     * untuk di OVERRICE
     * dieksekusi sebelum jobs akan dipecah ke
     */
    public function onBreakToNextExport()
    {

    }

    /**
     * saat jobs dipecah ke jobs selanjurnya
     */
    private function breakToNextExport(&$reader,$fileName,$lastExcelRow=1,$lastTableRow=1)
    {
        $this->appendExportLog('<br><span class="text-info">Break process to the next job, please wait</span>...<br>');
        if(!file_exists(public_path($this->_exportUploadPath))){
            mkdir(public_path($this->_exportUploadPath),0777,true);
        }

        Excel::save($reader,public_path($fileName));

        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        unset($objWriter, $reader);

        $resumParams = $this->getExportResumeParam();
        $resumParams['lastExcelRow'] = $lastExcelRow;
        $resumParams['lastTableRow'] = $lastTableRow;

        if($this->isExportJobsPerTenant()){
            ResExport::dispatch(
                self::class,
                $this->_exportAddsJobsParam,
                $this->_exportHomeUrl,
                $resumParams,
                $this->_tenantId
            )->onQueue('tenant'.$this->_tenantId);
        }else{
            ResExport::dispatch(
                self::class,
                $this->_exportAddsJobsParam,
                $this->_exportHomeUrl,
                $resumParams
            );
        }
    }

    private function breakExcelReader(&$reader,$fileName,$lastExcelRow=1,$lastTableRow=1)
    {
        
        if(!file_exists(public_path($this->_exportUploadPath))){
            mkdir(public_path($this->_exportUploadPath),0777,true);
        }

        Excel::save($reader,public_path($fileName));

        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        unset($objWriter, $reader);

        usleep(200);
        
        return  Excel::load(public_path($fileName), 'Xlsx', false);
    }



    /**
     * OVERRIDEABLE
     * format list data utama sebelum looping
     * 
     */
    public function formatExportMainData(array $data)
    {
        return $data;
    }

    public function formatExportExcelHeader(array $row1 = [])
    {
        $headerColumn=[];
        //jika ada format column maka gunakan format column
        if(!empty($this->_exportColumn)){
            $i=0;
            foreach($this->_exportColumn as $format){ 
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
     * OVERRIDEABLE
     * method untuk di overide untuk nambah pemformatan setelah formating default dieksekusi
     * 
     * @param array $headerColumn array row yang sudah diformat oleh formatExportExcelHeader
     * @param array $row1 array row pertama dari database (dari model) sebelum diformat formatExportExcelHeader
     * 
     * @return array
     */
    public function formatExportExcelHeaderAfter(array $headerColumn = [],array $row1 = [])
    {
        return $headerColumn;
    }

    /**
     * OVERRIDE hanya jika diperlukan, hindari sebisa mungkin.
     * untuk nambah pemformatan setelah formating default dieksekusi
     * 
     * @param array $row array row database (dari model)
     * 
     * @return array
     */
    public function formatExportExcelRow(array $row = [],int $rowNumber)
    {
        $insertRow=[];
        $i=0;
        //jika ada format column maka gunakan format column
        if(!empty($this->_exportColumn)){
            foreach($this->_exportColumn as $format){
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

    /**
     * convert nomor baris excel yang sedang di proses ke nomor urutan data yg sedang diproses
     * 
     * @param int $rowNumber baris excel ke berapa
     */
    protected function exportNumbering(int $rowNumber)
    {
        return $rowNumber - $this->getExportTemplateStartRow();
    }

    /**
     * method untuk di overide untuk nambah pemformatan setelah formating default dieksekusi
     * 
     * @param array $insertRow array row yang sudah diformat oleh formatExportExcelRow
     * @param array $row array row database (dari model) sebelum diformat formatExportExcelRow
     * 
     * @return array
     */
    public function formatExportExcelRowAfter(array $insertRow = [],array $row = [],int $rowNumber)
    {
        return $insertRow;
    }

    protected function setExportStartProcess()
    {
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        $config = $this->getInitExportStatus();
        $config['date'] = now()->format('Y-m-d');
        $config['log'] = '<b class="text-success">Start - generate download !</b><br>';
        $config['status'] = self::$EXPORT_STATUS_ON_PROGRESS;//1: onprogress

        $this->saveExportStatus($config); 
    }

    protected function setExportDone()
    {
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        $config = $this->getExportStatus(); 
        $config['log'] .= '<br><b class="text-success">Export Done !</b><br>';
        $config['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $config['status'] = self::$EXPORT_STATUS_SUCCESS;//2: success
        $this->saveExportStatus($config); 
    }

    protected function setExportFailed()
    {
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        $config = $this->getExportStatus(); 
        $config['log'] .= '<br><b class="text-danger">Export Failed !</b><br>';
        $config['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $config['filename'] = '';
        $config['urlFilename'] = '';
        // $config['processedCount'] = 0;
        $config['status'] = self::$EXPORT_STATUS_FAILED;//2: success
        $this->saveExportStatus($config); 
    }

    /**
     * set dari cronjob, jika cronjob ada uncaught error
     * 
     * @param Exception $exception instance Exception dari job failed
     */
    public function setExportJobFailed(Exception $exception)
    {        
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        $this->setExportFailed(); 

        $log = "<br><b class='text-danger'>Jobs terminated !</b>\n<hr>\n\nError message :<br>\n";
        $log .= $exception->getMessage();
        $log .= '<hr>';
        $log .= str_replace("\n",'<br>', $exception->getTraceAsString());

        $this->appendExportLog($log);
        report($exception); //lanjutkan error ke login (meureun)
    }

    /**
     * cek apakah bisa generate file export baru
     * 
     * @return boolean true jika bisa diproses, false jika sedang tidak bisa diproses
     */
    public function isExportReady()
    {
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        return $this->getExportStatus()['status'] != self::$EXPORT_STATUS_ON_PROGRESS;
    }

    /**
     * get status export terakhir
     */
    public function getExportStatus() 
    {        
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        if(!($config = $this->_getCache('export',$this->_exportGroup))){  
            $config = $this->getInitExportStatus();
        }

        $this->saveExportStatus($config);
        return $config;
    }

    protected function getInitExportStatus()
    {  
        $config = [];
        $config['log'] = '';
        $config['filename'] = '';
        $config['urlFilename'] = '';
        $config['count'] = 0;//jumlah total record yang harus diproses
        $config['processedCount'] = 0;//jumlah record yg sudah diproses
        $config['date'] = '';
        $config['status'] = self::$EXPORT_STATUS_READY;

        return $config;
    }

    protected function exportIncrementProcessedCount()
    {
        $config = $this->getExportStatus();
        $config['processedCount']++;
        $this->saveExportStatus($config);
    }

    protected function appendExportLog(string $log='')
    {        
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        $config = $this->getExportStatus();
        $config['log'] .= $log;
        $this->saveExportStatus($config);
    }

    protected function saveExportStatus($config) 
    {        
        if(!$this->_exportFunctionInitialize){
            return false;
        }

        $this->_saveCache('export',$this->_exportGroup,$config);
    }

}