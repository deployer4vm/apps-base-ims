<?php
namespace App\Base\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

use App\Jobs\ResImport;
use App\Facades\Excel;
use Carbon\Carbon;

/**
 * Excel Import Trait - fungsi-fungsi untuk handling import
 */
trait ResImportTrait { 

    // use ResCacheTrait;

    static $IMPORT_STATUS_READY = 0;//sedang tidak ada proses import
    static $IMPORT_STATUS_JUST_UPLOADED = 1;//sudah ada file upload
    static $IMPORT_STATUS_ON_PROGRESS = 2;//import sedang dalam proses
    static $IMPORT_STATUS_SUCCESS = 3;//import berhasil
    static $IMPORT_STATUS_FAILED = 4;//import gagal
    static $IMPORT_STATUS_ON_APPROVE_PROGRESS = 5;//sedang proses approve
    static $IMPORT_STATUS_ON_CANCEL_PROGRESS = 6;//sedang proses cancel

    private $_importFunctionInitialize = false;

    private $_importGroup = '';
    private $_importModelHeader = null;//instanse eloquent model untuk header table
    private $_importModelDetail = null;//instanse eloquent model untuk detail table
    private $_importModelDetailFK = '';
    private $_importAddsJobsParam = [];//parameter tambahan ke jobs parameter
    private $_importStartRow = 2;//start read dari baris berapa
    private $_importUseJobs = true;//sementara belum ada opsi pake jobs atau tidak, HARUS pake jobs
    private $_importUploadPath = 'import/';//path ke upload relative dari public_path

    private $_importDefaultColumn = [];//daftar field yang diimport, jika array kosong maka semua field diimport
    private $_importColumn = [];//daftar field yang diimport, jika array kosong maka semua field diimport
    private $_resumeParams = [];

    public function initImport(string $group = '', $modelHeader=null, $modelDetail=null, array $addsJobsParam=[])
    {
        $this->_importGroup = $group;

        $this->setImportModel($modelHeader,$modelDetail);
        $this->setImportJobsParam($addsJobsParam);

        $this->_importFunctionInitialize = true;
        
    }

    /**
     * import jobs param
     * -----
     */
    public function getImportJobsParam()
    {
        return $this->_importAddsJobsParam;
    }

    public function setImportJobsParam(array $addsJobsParam = [])
    {
        $this->_importAddsJobsParam = $addsJobsParam;
    }

    /**
     * import model
     * -----
     */
    public function getImportModelHeader()
    {
        return $this->_importModelHeader;
    }

    public function getImportModelDetail()
    {
        return $this->_importModelDetail;
    }

    public function setImportModel($modelHeader,$modelDetail)
    {
        if($modelHeader)
            $this->setImportDetailForeignKey($modelHeader->getModel()->getTable().'_id');
        $this->_importModelHeader = $modelHeader;
        $this->_importModelDetail = $modelDetail;
    }

    public function setImportDetailForeignKey(string $key)
    {
        $this->_importModelDetailFK = $key;
    }

    public function getImportDetailForeignKey()
    {
        return $this->_importModelDetailFK;
    }
    
    /**
     * import upload path
     * -----
     */
    public function getImportUploadPath()
    {
        return $this->_importUploadPath;
    }

    public function setImportUploadPath(string $uploadPath='')
    {
        $this->_importUploadPath = trim(trim($uploadPath,'/'),'\\').'/';
    }

    /**
     * import start row
     * -----
     */
    public function getImportStartRow()
    {
        return $this->_importStartRow;
    }

    public function setImportStartRow(int $startRow=2)
    {
        $this->_importStartRow = $startRow;
    }

    /**
     * Import column, list field yg di-Import
     * -----
     */
    public function getImportColumn()
    {
        return $this->_importColumn;
    }

    /**
     * @param array $columnImport format : 
     *  [
     *      'EXCEL_COLUMN' =>
     *          ['field_name', [
     *              'caption'=>'CAPTION COLUMNNYA',
     *              'default' => DEFAULT VALUE YANG DIGUNAKAN JIKA VALUE KOSONG ATAU JIKA TIDAK SESUAI FORMAT/TYPE
     *              'type' => 'type' ---> string, number, date, auto (default)
     *              'format' =>  --> format tambahan dari type, misal type date isi format 'Y-m-d'
     *              ]
     *          ],
     *      'EXCEL_COLUMN_SELANJUTNYA' =>
     *          [..field selanjutny]
     *  ]
     */
    public function setImportColumn(array $columnImport = [])
    {
        $this->_importColumn = $columnImport;
    }
    
    public function setImportDefaultColumn(array $columnImport = [])
    {
        $this->_importDefaultColumn = $columnImport;
    }
    public function getImportDefaultColumn()
    {
        return $this->_importDefaultColumn;
    }
    
    
    /**
     * OVERRIDEABLE
     * fungsi untuk di overload di parent repo yg menggunakan import trait ini (jika diperlukan)
     * method ini di eksekusi di job
     */
    public function initImportOnJob(array $addsJobsParam=[])
    {
        //jika ternyata tidak mengoverload method ini,
        //maka saat method ini dieksekusi di job, cek apakah initImport sudah diproses, jika belum maka eksekusi
        if(!$this->_importFunctionInitialize){
            $this->initImport($this->_importGroup,$this->_importModelHeader,$this->_importModelDetail, $addsJobsParam);
            $this->_importFunctionInitialize = true; 
        }              
    }

    
    /**
     * upload file excel import dan
     * 
     * @param Request $inputFile request input file
     * @param int $startRow start row ke berapa data mulai diread
     * @param array $addsData data tambahan untuk diinsert ke model header
     * @param string $transactionDate tanggal transaksi saat ini
     * 
     * @return false|object record header
     */
    public function startImport($inputFile,int $startRow=2,array $addsData = [],string $transactionDate='')
    {        
        if(!$this->_importFunctionInitialize){
            return false;
        }
        $this->setImportStartRow($startRow);
        //generate path file import akan diupload
        $path = $this->getImportUploadPath().strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_',$this->_importGroup));
        //simpan nama file aslinya
        $fileName = $inputFile->getClientOriginalName();
        //upload file import nya
        $filePath = $inputFile->store($path);
        //proses jika upload berhasil
        if($filePath){
            try{
                if($this->_importModelHeader){
                    $addsData['import_filepath'] = $filePath;
                    $addsData['import_filename'] = $fileName;
                    $addsData['import_log'] = '';
                    $addsData['import_status'] = 0;
                    $addsData['is_import'] = 1;
                    $addsData['created_at'] = now();
                    $result = $this->_importModelHeader->create($addsData);                    
                    if(!$result){
                        $this->error = 'Insert error.';
                        return false;
                    }    
                    
                    $config = $this->getInitImportStatus();
                    $config['addsData'] = $addsData;
                    $this->saveImportStatus($config); 
                }

                $this->setImportStartProcess([
                    'filenamePath' => $filePath,
                    'filename' => $fileName,
                    'transactionDate' => $transactionDate?$transactionDate:now()->format('Y-m-d')
                ]);

                $config =  $this->getImportStatus();
                // mulai jobs untuk proses import nya
                ResImport::dispatch(
                    self::class,
                    $this->getImportStartRow(),
                    $this->_importAddsJobsParam
                );

                return $config;
            }catch (Exception $e) {
                $this->error = $e->getMessage();
            }
        }else{
            $this->error = 'File import tidak terdeteksi.';
        }     
        return false;
    }
    
    /**
     * set params import, sebagai penanda bahwa jobs ini adalah kelanjutan dari jobs sebelumnya
     * (jika si $resumeParams nya tidak kosong)
     */
    public function setImportAsResume(array $resumeParams = [])
    {
        $this->_resumeParams = $resumeParams;
        if(!empty($this->_resumeParams))$this->onImportResume();

    }
    public function getImportResumeParam()
    {
        return $this->_resumeParams;
    }

    /**
     * untuk diOVERRIDE
     * dieksekusi saat pertama kali import diresume
     */
    public function onImportResume()
    {

    }

    /**
     * proses file import yang sudah diupload
     */
    public function importProcess()
    {
        ini_set('memory_limit','1024M');
        set_time_limit(0);
        
        $startTime = microtime(true);

        if(!$this->_importFunctionInitialize){
            return false;
        }

        $config = $this->getImportStatus();
        $header = [];
        if($this->_importModelHeader){
            $header = $this->_importModelHeader->where('import_status',0)->where('is_import',1)->first();
            if(!$header){

                $this->appendImportLog('<b class="text-danger">Import file not found!</b><br>');
                $this->setImportFailed();

                $this->error = 'Tidak ada file import yang sudah diupload';
                return false;
            }
            $header = $header->toArray();
        }else{
            $header = $config['addsData'];
        }

        /**
         * proses import
         */
        
        //jika jobs pertama maka
        if(empty($this->_resumeParams)){
            
            $this->appendExportLog('<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>...<br>');
            $this->appendImportLog('Load file <b>'.$config['filename'].'</b><br>'); 

            $reader = Excel::load(Storage::path($config['filenamePath']),'xlsx',false);
            $reader->setActiveSheetIndex(0);
    
            $startRecord = $this->getImportStartRow();
    
            $this->appendImportLog('Reading excel data, please wait...<br>');
    
            //read data di file excel
            $data = Excel::readRow($reader,$startRecord,500,[$this, 'importReadExcelCall']);
    
            $config['count'] = count($data);
            $config['log'] .= '<br><b class="text-info">Read complete !</b><br/>';
            $config['log'] .= 'Data count : <b>'.$config['count'].'</b><br/>...<br/>';
            $config['log'] .= 'Start importing to database, please wait...<br/>';
            $this->saveImportStatus($config); 
            $row=1;
        }else{
            $this->appendImportLog('<span class="text-info">Continueing process from previous jobs</span>...<br>');            
            $this->appendExportLog('<span class="text-info">Jobs started at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>...<br>');

            $this->appendImportLog('Reload file <b>'.$config['filename'].'</b><br>'); 

            $reader = Excel::load(Storage::path($config['filenamePath']),'xlsx',false);
            $reader->setActiveSheetIndex(0);

            $this->appendImportLog('Re-Reading excel data, please wait...<br>');
            
            $startRecord = $this->getImportStartRow() + $this->_resumeParams['lastExcelRow'];
    
            //read data di file excel
            $data = Excel::readRow($reader,$startRecord,500,[$this, 'importReadExcelCall']);
            
            $row=$this->_resumeParams['lastExcelRow']+1;
        }
        
        /**
         * ubah format column header caption dari lib Excel ke format column import
         */
        $defaultHeaderColumn = array_map(function($v){
            return [strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_',$v)),[]];
        },Excel::getColumnHeader());
        $this->setImportDefaultColumn($defaultHeaderColumn);

        /**
         * Mulai pross insert ke table detail
         */
        foreach($data as $col => $val){
            
            //convert row excel ke struktur insert sesuai fungsi yang 
            $insertRow = $this->formatImportExcelRowAfter(
                $this->formatImportExcelRow($val,$header,$row),
                $val,
                $header,
                $row
            );

            //jika kosong berarti error, maka skip
            if(empty($insertRow)){
                $row++;
                continue;
            }
            
            $insertRow['created_at'] = now();
            $result = $this->_importModelDetail->create($insertRow);

            //cek apakah insert berhasil
            if($result){
                $this->appendImportLog('. ');
            }else{
                $this->appendImportLog('<b class="text-danger">. [FAIL : '.$row.'] SKIPPED</b> ');
            }

            $this->importIncrementProcessedCount();
            //break proses setiap kurang dari 1 jam
            if((microtime(true)-$startTime)>=3500){
                $data = null;
                unset($data);
                $this->onBreakToNextImport();
                $this->breakToNextImport($reader,$row);
                return true;
            }
            $row++;
            usleep(1000);
        }
        
        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        $data = null;
        unset($reader,$data);

        $this->importProcessAfter();

        //ubah status jadi ok
        $this->setImportFinish();
        return true;        
    }
    
    /**
     * untuk di OVERRICE
     * dieksekusi sebelum jobs akan dipecah ke next job
     */
    public function onBreakToNextImport()
    {

    }

    /**
     * diset
     */
    private function breakToNextImport(&$reader,$lastExcelRow=1)
    {
        $this->appendImportLog('<br><span class="text-info">Break process to the next job, please wait</span>...<br>');
        
        //pastikan semua selesai dan memory di-free-kan kembali
        $reader->disconnectWorksheets();// Good to disconnect
        $reader->garbageCollect(); // Add this too
        $reader = null;
        unset($reader);

        $resumParams = $this->getImportResumeParam();
        $resumParams['lastExcelRow'] = $lastExcelRow;
        ResImport::dispatch(
            self::class,
            $this->getImportStartRow(),
            $this->_importAddsJobsParam,
            $resumParams
        );
    }
    
    /**
     * untuk di OVERRIDE
     * Untukprocess lanjutan setelah insert semua ke database sebelum finish
     */
    public function importProcessAfter()
    {

    }

    /**
     * call back per row excel yg diread, jika diperlukan, defaultnya tambah log titik sebagai tanda progress.
     * 
     * @param array $row array row excel
     * @param int $rowNumber data ke berepa yg sedang diproses
     * 
     */
    public function importReadExcelCall(array $row = [],int $rowNumber)
    {
        $this->appendImportLog('. ');
    }

    /**
     * memformat row excel menjadi array insert database
     * 
     * @param array $row array row excel
     * @param array $header array row database data header atau addsData dari status
     * @param int $rowNumber nomor urut baris/data saat ini yg sedang diproses
     * 
     * @return array
     */
    public function formatImportExcelRow(array $row = [],array $header = [],int $rowNumber)
    {
        $insertRow=[];
        $i=0;
        //ambil format maping kolom excel ke field database beserta konfig formatnya
        $importColumn = empty($this->_importColumn)?$this->getImportDefaultColumn():$this->_importColumn;
        
        foreach($importColumn as $column => $format){
            $i++; 
            //jika required
            if(isset($format[1]['required']) && empty($row[$column])){
                $this->appendImportLog('<b class="text-danger">. [row : '.$rowNumber.', column : '.$column.' is required] SKIPPED</b>');
                $insertRow = [];
                break;
            }else{
                $insertRow[$format[0]] = 
                    empty($row[$column]) && isset($format[1]['default'])?
                    $format[1]['default']:
                    $this->importFormatRowValue($row[$column],$format[1]);
            }

        }

        $insertRow['is_import'] = 1;

        //tambahkan field foreign key ke table header dari table detail
        if($this->_importModelHeader)
            $insertRow[$this->getImportDetailForeignKey()] = isset($header['id'])?$header['id']:0;

        return $insertRow;
    }

    /**
     * Untuk nambah pemformatan setelah formating default dieksekusi.
     * method untuk di overide, untuk menyesuaikan kolom mana diinsert ke field mana
     * 
     * @param array $insertRow array row yang sudah diformat oleh formatImportExcelRow
     * @param array $row array row dari excel
     * @param array $header array row database data header
     * @param int $rowNumber nomor urut baris/data saat ini yg sedang diproses
     * 
     * @return array
     */
    public function formatImportExcelRowAfter(array $insertRow = [],array $row = [],array $header = [],int $rowNumber)
    {
        return $insertRow;
    }

    /**
     * memformat value cell sesuai config format columnya
     * 
     * @param mixed value per cell/field dari database
     * @param array format, format dari konfig column, format :
     *  [
     * 
     *      'required'=>true, --> sertakan jika field ini harus terisi
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

    /**
     * tandai proses import sudah mulai
     * 
     * @param array $fileRecord array filename, filepath & transactionDate
     */
    protected function setImportStartProcess(array $fileRecord)
    {
        if(!$this->_importFunctionInitialize){
            return false;
        }

        $config = $this->getInitImportStatus();
        $config['filename'] = $fileRecord['filename'];
        $config['filenamePath'] = $fileRecord['filenamePath'];
        $config['transactionDate'] = $fileRecord['transactionDate'];
        $config['log'] = '<b class="text-success">Start - import !</b><br>';
        $config['status'] = self::$IMPORT_STATUS_ON_PROGRESS;//1: onprogress

        $this->saveImportStatus($config); 
    }

    /**
     * set proses import selesai dan berhasil (tapi belum approve)
     */
    protected function setImportFinish()
    {
        $config = $this->getImportStatus(); 
        $config['log'] .= '<br><b class="text-success">Import Finished !</b><br>';
        $config['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $config['status'] = self::$IMPORT_STATUS_SUCCESS;//3: success
        $this->saveImportStatus($config); 
    }

    /**
     * set proses import selesai tapi gagal
     */
    protected function setImportFailed()
    {
        if(!$this->_importFunctionInitialize){
            return false;
        }
        
        $config = $this->getImportStatus();     
        Storage::delete($config['filenamePath']); 
        $config['log'] .= '<br><b class="text-danger">Import Failed !</b><br>';
        $config['log'] .= '<span class="text-info">Jobs ended at : <b>'.now()->format('Y-m-d H:i:s').'</b></span>';
        $config['filename'] = '';
        $config['filenamePath'] = '';
        $config['status'] = self::$IMPORT_STATUS_FAILED;//4: failed
        $this->saveImportStatus($config); 
    }

    /**
     * set dari cronjob, jika cronjob ada uncaught error
     */
    public function setImportJobFailed(Exception $exception, string $log = '')
    { 
        if(!$this->_importFunctionInitialize){
            return false;
        }

        $this->setImportFailed(); 

        $log = "<br><b class='text-danger'>Jobs terminated !</b>\n<hr>\n\nError message :<br>\n";
        $log .= $exception->getMessage();
        $log .= '<hr>';
        $log .= str_replace("\n",'<br>', $exception->getTraceAsString());

        $this->appendImportLog($log);
        report($exception); //lanjutkan error ke login (meureun)
    }

    /**
     * BLOCK APPROVE PROCESS
     */
    /**
     * set saat meng-approve data2 yg sudah diimport.
     * Override method ini untuk memanipulasi proses approve
     */
    public function importApprove()
    { 
        $header = [];
        if($this->_importModelHeader)
            $header = $this->_importModelHeader->where('import_status',0)->where('is_import',1)->first()->toArray();
        $this->importApproveProcess($header);
        return $this->setImportApproveDone(); 
    }

    /**
     * UNTUK DI OVERRIDE
     */
    public function importApproveProcess(array $headerRecord=[])
    {

    }

    /**
     * tandai proses sebagai "approve process on progress". Digunakan untuk proses approve yg
     * memerlukan proses tambahan yg cukup lama, misal via jobs,
     */
    public function setImportApprove()
    {
        $config = $this->getImportStatus();   
        $config['log'] .= '<br><b class="text-info">Import Approved !</b><br>';
        $config['status'] = self::$IMPORT_STATUS_ON_APPROVE_PROGRESS;
        $this->saveImportStatus($config);
        return $config;
    }

    
    /**
     * tandai proses sebagai telah beres di approve, jadi bisa melakukan import yg lain
     */
    public function setImportApproveDone()
    {   
        $config = $this->getImportStatus();
        Log::info('Import Approve '.$this->_importGroup.' DONE : '.$config['log']);
        if($this->_importModelHeader){
            $importHeader = ['import_status'=>1,'import_log'=>$config['log']];   
            $this->_importModelHeader->where('import_status',0)->where('is_import',1)->update($importHeader);
        }
        $this->_importModelDetail->where('import_status',0)->where('is_import',1)->update(['import_status'=>1]);

        $this->saveImportStatus($this->getInitImportStatus()); 
        return $this->getImportStatus();
    }

    /**
     * BLOCK CANCEL PROCESS
     */
    
     /**
     * saat proses import yang sudah selesai di cancel
     */
    public function ImportCancel()
    {
        $header = [];
        if($this->_importModelHeader)
            $header = $this->_importModelHeader->where('import_status',0)->where('is_import',1)->first()->toArray();
        $this->setImportCancelProcess($header);
        return $this->setImportCancelDone();
    }

    public function setImportCancelProcess(array $headerRecord=[])
    {

    }
    /**
     * tandai proses sebagai "cancel process on progress". Digunakan untuk proses cancel yg
     * memerlukan proses tambahan yg cukup lama, misal via jobs,
     */
    public function setImportCancel()
    {
        $config = $this->getImportStatus();   
        $config['log'] .= '<br><b class="text-danger">Import Canceled !</b><br>';
        $config['status'] = self::$IMPORT_STATUS_ON_CANCEL_PROGRESS;
        $this->saveImportStatus($config);
        return $config;
    }
    /**
     * saat proses import yagn sudah selesai di cancel
     */
    public function setImportCancelDone()
    {
        $config = $this->getImportStatus();  
        Log::info('Import Canceled '.$this->_importGroup.' DONE : '.$config['log']);

        if($this->_importModelHeader)
            $this->_importModelHeader->where('import_status',0)->where('is_import',1)->delete();

        $this->_importModelDetail->where('import_status',0)->where('is_import',1)->delete();
   
        Storage::delete($config['filenamePath']); 

        $this->saveImportStatus($this->getInitImportStatus()); 
        return $this->getImportStatus();
    }

    /**
     * cek apakah sekarang dalam konsisi bisa tambah import baru
     * 
     * @return boolean true jika bisa tidak ada import yg sedang diproses, false jika sedang ada proses import
     */
    public function canNewImport()
    {
        return $this->_importModelDetail->where('import_status',0)->where('is_import',1)->exists()?false:true;        
    }

    /**
     * cek apakah ada file import yg sudah diupload dan bisa untuk diproses, jika sedang diproses maka akan false
     * 
     * @return boolean true jika bisa diproses, false jika sedang tidak bisa diproses
     */
    public function isImportReady()
    {
        return $this->getImportStatus()['status'] == self::$IMPORT_STATUS_READY && !$this->canNewImport();
    }

    /**
     * 
     */
    public function getImportStatus() 
    {        
        if(!$this->_importFunctionInitialize){
            return false;
        }
        
        if(!($config = $this->_getCache('import',$this->_importGroup))){  
            $config = $this->getInitImportStatus();
        }
        $this->saveImportStatus($config);
        return $config;
    }

    /**
     * 
     */
    protected function getInitImportStatus()
    {  
        $config = [];
        $config['status'] = self::$IMPORT_STATUS_READY;
        $config['log'] = '';
        $config['filename'] = '';
        $config['filenamePath'] = '';
        $config['count'] = 0;
        $config['processedCount'] = 0;
        $config['transactionDate'] = now()->format('Y-m-d');
        $config['addsData'] = [];

        return $config;
    }

    protected function importIncrementProcessedCount()
    {
        $config = $this->getImportStatus();
        $config['processedCount']++;
        $this->saveImportStatus($config);
    }

    protected function appendImportLog(string $log='')
    {
        if(!$this->_importFunctionInitialize){
            return false;
        }
        
        $config = $this->getImportStatus();
        $config['log'] .= $log;
        $this->saveImportStatus($config);
    }

    protected function saveImportStatus($config) 
    {  
        if(!$this->_importFunctionInitialize){
            return false;
        }

        $this->_saveCache('import',$this->_importGroup,$config);
    }
    
    protected function excelToDateTimeObject($value,$config) 
    {        
        try{        
            $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $value->format('Y-m-d');
        } catch (Exception $e) {
            try{        
                $value = Carbon::createFromFormat('d/m/Y', $value);
                return $value->format('Y-m-d');
            }catch (Exception $e2) {
                report($e2);
                $config['log'] .= "<b class='text-danger'>Warning > </b> Format tanggal ".$value." tidak terdeteksi, gunakan format <b>YYYY-MM-DD</b><br>\n";
                $config['log'] .= "<b class='text-danger'>Jobs terminated!</b><br>\n Silahkan batalkan proses import dan perbaiki format tanggal yang keliru, lalu ulangi proses import.";
                $config['filename'] = '';
                $config['status'] = 3;//3: failed
                $this->saveImportStatus($config);
                Storage::delete($config['filename']);
                die();
            }
        }
    }
    
    /**
     * list item-item yang sedang proses import
     */
    public function listImport(array $filter=[], int $offset=0, int $limit = 0,array $orderBy = [])
    {        
        return $this->_list($this->_importModelDetail->where('import_status',0)->where('is_import',1),$filter,$offset,$limit,$orderBy);
    }
}