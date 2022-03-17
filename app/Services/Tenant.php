<?php

namespace App\Services;

use Exception;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Base\BaseRepository;

use App\Models\Tenant as MTenant;
use App\Models\TenantGroup;
use App\Models\TenantGroupTenant;

class Tenant extends BaseRepository
{   
    
    protected $autoResource = [
        'Tenant' => ['r'=>MTenant::class,'w'=>MTenant::class],
        'Group' => ['r'=>TenantGroupTenant::class,'w'=>TenantGroupTenant::class],
    ];
    
    /**
     * apakah yang sekarang aktif adalah project dan tenant id yg diinput
     */
	public function isCurrentTenant($project, $tenantId)
	{
		$project_code = config('AppConfig.client.project_code');
		if($project_code != $project){
			return false;
		}
        
		if (is_array($tenantId)) {
			if(!in_array(config('tenant.instance_data.id'), $tenantId)){
				return false;
			}
		}else{
			if(config('tenant.instance_data.id') != $tenantId){
				return false;
			}
		}

		return true;
	}

    /**
     * START - GROUP MANAGE PEMISAHAN DATABASE ATAU TABLE PER TENANT
     */

    /**
     * generate nama koneksi database per tenant
     */
    public function getDbConnectionName($tenantId)
    {
        if(config('AppConfig.system.multitenant.data_mode',1) != 3)
            return config('database.default');

        return config('database.perTenant').$tenantId;
    }

    /**
     * generate and get connection database pertenant
     */
    public function getDbConnection($tenantId)
    {
        if(config('AppConfig.system.multitenant.data_mode',1) != 3)
            return config('database.connections.'.config('database.default'));

        $dbConfigName = $this->getDbConnectionName($tenantId);

        // jika multidatabase server aktif maka detek dan sinkronkan konfig db nya
        if(config('database.multi_database_server.enable',false)){            
            if(!($dbConfig = $this->getDbConnection_getServer($tenantId))){
                return false;
            }
        }else{
            $dbConfig = config('database.connections.'.config('database.perTenant'));
        }

        $dbConfig['database'] = $dbConfig['database_prefix'].$tenantId;
        $dbConfig['name'] = $dbConfig['name'].' '.$tenantId;

        config(['database.connections.'.$dbConfigName => $dbConfig]);
        
        return $dbConfig;
    }

        private function getDbConnection_getServer($tenantId)
        {
            if(config('tenant.id')!=$tenantId){
                if(!($tenant = $this->_getTenantById($tenantId)))
                    return false;
                $server = config('database.multi_database_server.servers.'.$tenant->db);
            }else{
                $server = config('database.multi_database_server.servers.'.config('tenant.db'));
            }
            
            return $server;
        }

    private $_tmpTenantList=[],
    $_tmpTenantListByGroupApp=[],
    $_tmpTenantListByDomain=[];
    
    private function _getTenantById($tenantId)
    {
        if(!isset($this->_tmpTenantList[$tenantId])){
            
            if(!($this->_tmpTenantList[$tenantId] = $this->getTenantModel()->where('id',$tenantId)->first())){
                $this->error = 'Tenant not found';
                return false;
            }

            $this->_tmpTenantListByGroupApp[$this->_tmpTenantList[$tenantId]['group_app']] = $this->_tmpTenantList[$tenantId];
            $this->_tmpTenantListByDomain[$this->_tmpTenantList[$tenantId]['domain']] = $this->_tmpTenantList[$tenantId];
        }

        return $this->_tmpTenantList[$tenantId];
    }

    
    private function _getTenantByGroupApp($groupApp)
    {
        if(!isset($this->_tmpTenantListByGroupApp[$groupApp])){
            $this->_tmpTenantListByGroupApp[$groupApp] = $this->getTenantModel()->where('group_app',$groupApp)->first();
            $this->_tmpTenantList[$this->_tmpTenantListByGroupApp[$groupApp]['id']] = $this->_tmpTenantListByGroupApp[$groupApp];
            $this->_tmpTenantListByDomain[$this->_tmpTenantListByGroupApp[$groupApp]['domain']] = $this->_tmpTenantListByGroupApp[$groupApp];
        }

        return $this->_tmpTenantListByGroupApp[$groupApp];
    }

    private function _getTenantByDomain($domain)
    {
        if(!isset($this->_tmpTenantListByDomain[$domain])){
            $this->_tmpTenantListByDomain[$domain] = $this->getTenantModel()->where('domain',$domain)->first();
            $this->_tmpTenantListByGroupApp[$this->_tmpTenantListByDomain[$domain]['group_app']] = $this->_tmpTenantListByDomain[$domain];
            $this->_tmpTenantList[$this->_tmpTenantListByDomain[$domain]['id']] = $this->_tmpTenantListByDomain[$domain];
        }

        return $this->_tmpTenantListByDomain[$domain];
    }

    /**
     * get
     */
    public function getDbRawPDO($tenantId=false)
    {
        return $this->db($tenantId)->getRawPdo();
    }

    /**
     * generate and get nama database untuk database pertenant
     */
    public function getDbName($tenantId)
    {        
        if(!($dbConfig = $this->getDbConnection($tenantId)))
            return false;
        return $dbConfig['database'];

        // if(config('AppConfig.system.multitenant.data_mode',1) != 3)
        //     return config('database.connections.'.config('database.default').'.database');

        // return config('database.connections.'.config('database.perTenant').'.database_prefix').$tenantId;
    }

    /**
     * cek apakah database pertenant sudah ada
     */
    public function dbExists($tenantId)
    {
        // $schemaName = config("database.connections.".config("database.perTenant").".database_prefix").$tenantId;
        $schemaName = $this->getDbName($tenantId);
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $db = DB::select($query, [$schemaName]);
        
        //jika empty berarti database belum ada
        return empty($db)?false:true;
    }

    public function tableExists($tableName,$tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $this->getDbConnection($tenantId);// generate dulu confignya
        return Schema::connection($this->getDbConnectionName($tenantId))->hasTable($tableName);
    }
    
    /**
     * set connection active database per tenant session saat ini
     */
    public function setDb($tenantId)
    {        
        // data_mode : 1 dalam table yg sama, 2 dalam table berbeda tp database sama, 3 beda database
        if(config('AppConfig.system.multitenant.data_mode',1) != 3)
            return true;
            
        $dbConfigName = $this->getDbConnectionName($tenantId);
        config(['tenant.connection',$dbConfigName]);

        // tambah connection database on thy fly sesuai tenant yang aktifnya (jika belum ditambah)
        if(config('database.connections.'.$dbConfigName,false)==false){
            $dbConfig = $this->getDbConnection($tenantId);
            config(['database.connections.'.$dbConfigName => $dbConfig]);
        }
        return true;
    }

    /**
     * memigrasikan seluruh migrasi per tenant di 1 tenant baru
     */
    public function migrate($tenantId)
    {
        //jika database belum ada maka tolak
        if (!$this->dbExists($tenantId)) {
            return false;
        }

        $migrations = config('hpsynapse.migration_path');
        foreach ($migrations as $migrationpath) {
            $migrationFileList = glob($migrationpath.DIRECTORY_SEPARATOR.'*.php');
            foreach ($migrationFileList as $migration) {  
                include_once $migration;
                $migrationClass = ucfirst(Str::camel(substr(str_replace('.php','',basename($migration)),18)));                
                $tmpClass = new $migrationClass;
                if(method_exists($tmpClass,'tenantMigrateMode')){
                    if(!property_exists($tmpClass,'tenantId') || $tmpClass->tenantId==$tenantId){
                        $tmpClass->setTenantMigrateMode(true);
                        $tmpClass->setTenantId($tenantId);
                        $tmpClass->up();
                    }
                }
            }
        }
        
        return true;
    }
    
    /**
     * create database per Koperasi, saat ini hanya support MariaDB/MySQL
     */
    public function createDatabase($tenantId,$dbServerId=0)
    {
        if(
            ($dbServerId==0 && config('xmlapi.dbcreate_use_cpanel')) ||
            ($dbServerId!=0 && config("database.multi_database_server.servers.".$dbServerId.".cpanel.dbcreate_use_cpanel",false)) 
        ) {
            return $this->createDatabaseCpanel($tenantId,$dbServerId);
        }else{
            return $this->createDatabaseSql($tenantId,$dbServerId);
        }
    }

    /**
     * create database menggunakan query sql
     */
    public function createDatabaseSql($tenantId,$dbServerId=0)
    {
        
        //jika database sudah ada maka tolak
        if ($this->dbExists($tenantId)) {
            return false;
        }

        // jika di server db utama
        if($dbServerId==0){
            $schemaName = config("database.connections.".config("database.perTenant").".database_prefix").$tenantId;
            $charset = config("database.connections.".config("database.perTenant").".charset",'utf8mb4');
            $collation = config("database.connections.".config("database.perTenant").".collation",'utf8mb4_general_ci');
            DB::statement("CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;");
        }else{
            $schemaName = config("database.multi_database_server.servers.".$dbServerId.".database_prefix").$tenantId;
            $charset = config("database.multi_database_server.servers.".$dbServerId.".charset",'utf8mb4');
            $collation = config("database.multi_database_server.servers.".$dbServerId.".collation",'utf8mb4_general_ci');

            $pdo = new \PDO(
                "mysql:host=".config("database.multi_database_server.servers.".$dbServerId.".host"), 
                config("database.multi_database_server.servers.".$dbServerId.".username"), 
                config("database.multi_database_server.servers.".$dbServerId.".password")
            );
            // $pdo = Tenant::getDbRawPDO($koperasiId);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;");
        }
        // generate connection config per tenant nya
        // Tenant::getDbConnection($koperasiId);
        // DB::connection(Tenant::getDbConnectionName($koperasiId))->statement("CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;");
        
        return true;
    }

    /**
     * create database menggunakan api cpanel
     */
    public function createDatabaseCpanel($tenantId,$dbServerId=0)
    {
        
        // jika di server db utama
        if($dbServerId==0){
            $dbPrefix = config("database.connections.".config("database.perTenant").".database_prefix");
            $cpanel = [
                'username' => config('xmlapi.username'), 
                'password' => config('xmlapi.password'), 
                'domain' => config('xmlapi.domain')        
            ];
        }else{
            $dbPrefix = config('database.multi_database_server.servers.'.$dbServerId.'.database_prefix');
            $cpanel = [
                'username' => config('database.multi_database_server.servers.'.$dbServerId.'.cpanel.username'), 
                'password' => config('database.multi_database_server.servers.'.$dbServerId.'.cpanel.password'), 
                'domain' => config('database.multi_database_server.servers.'.$dbServerId.'.cpanel.domain')        
            ];            
        }

        $schemaName = $dbPrefix.$tenantId;
        $uapi = new \App\Services\cpanelAPI(
            $cpanel['username'], 
            $cpanel['password'], 
            $cpanel['domain']
        ); //instantiate the object
        $ret = $uapi->uapi->Mysql->create_database(array('name' => $schemaName));

        if($ret->status==0)
            $this->error = $ret->errors[0];        

        return $ret->status?true:false;
    }

    /**
     * generate nama table dengan prefix tenant
     */
    public function getTableName($tableName,$tenantId)
    {
        return config('AppConfig.system.multitenant.table_prefix','_').$tenantId.'_'.$tableName;
    }

    /**
     * DB TRANSACTION PER TENANT CONNECTION
     */

    /**
     * JANGAN DIGUNAKAN DULU - KAYANYA MASIH BUG BELUM TESTING LAGI
     * detek otomatis dbtransaction
     * 
     * @param Object $that $this dari class bersangkutan
     * @param Function $func callback fungsi yang akan dieksekusi dengan format function($that)
     * @param Function $rollbackFunc callback fungsi saat terjadi error
     * 
     * @return Boolean true jika berhasil, false atau throw error jika gagal
     */
    public function dbBeginTransactionIfNotExist($that, $func, $rollbackFunc=null)
    {
        // jika belum ada transaksi aktif maka aktifkan
        $dontHaveTransactionLevel = !$this->dbTransactionLevel();
        
        try {         
            if($dontHaveTransactionLevel) 
                $this->dbBeginTransaction();

            $return = $func($that);
            
            if($dontHaveTransactionLevel) 
                $this->dbCommit();

        } catch (Exception  $e) {
            $return = false;
            
            if($dontHaveTransactionLevel) 
                $this->dbRollback();

            $this->error = $e->getMessage(); 

            Log::error('dbBeginTransactionIfNotExist ERROR');
            Log::error($e);

            // eksekusi rollback function jika disertakan
            if($rollbackFunc!=null)   
                $rollbackFunc($that);      
            
            // jika sedang dalam transaksi dari parent maka teruskan error nya ke parent transaction nya
            if(!$dontHaveTransactionLevel) 
                throw new Exception($this->errorFull());
        }
            
        return $return;
    }

    /**
     * cek transaction level
     */
    public function dbTransactionLevel($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        // Log::info('Tenant - transaction level : '.$tenantId.' - '.$this->getDbConnectionName($tenantId));
        return DB::connection($this->getDbConnectionName($tenantId))->transactionLevel();
    }

    /**
     * begin db transaction pertenant, hanya eksekusi di multi tenant db yg sudah di-initialize sebelumnya
     */
    public function dbBeginTransaction($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        // Log::info('Tenant - transaction begin : '.$tenantId.' - '.$this->getDbConnectionName($tenantId));
        DB::connection($this->getDbConnectionName($tenantId))->beginTransaction();
    }

    /**
     * commit db transaction pertenant, hanya eksekusi di multi tenant db yg sudah di-initialize sebelumnya
     */
    public function dbCommit($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        // Log::info('Tenant - transaction commit : '.$tenantId.' - '.$this->getDbConnectionName($tenantId));
        DB::connection($this->getDbConnectionName($tenantId))->commit();
    }

    /**
     * rollback db transaction pertenant, hanya eksekusi di multi tenant db yg sudah di-initialize sebelumnya
     */
    public function dbRollback($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        // Log::info('Tenant - transaction rollback : '.$tenantId.' - '.$this->getDbConnectionName($tenantId));
        DB::connection($this->getDbConnectionName($tenantId))->rollback();
    }

    /**
     * db per tenant
     */
    public function db($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $this->getDbConnection($tenantId);// generate dulu confignya
        return DB::connection($this->getDbConnectionName($tenantId));
    }

    /**
     * END - GROUP MANAGE PEMISAHAN DATABASE ATAU TABLE PER TENANT
     */
        
    /**
     * START - GROUP MANAGE ACTIVE TENANT
     */
    private function getTenantModel()
    {
        if(config('AppConfig.system.multitenant.table_instance',false)==false){
            return new MTenant;
        }

        return MTenant::with(['instanceData']);
    }
    
    public function setActiveTenantById($tenantId)
    {
        $tenant = $this->_getTenantById($tenantId);//$this->getTenantModel()->where('id',$tenantId)->first();
        if($tenant)
            $this->setActiveTenant($tenant->toArray());
    }
    
    public function setActiveTenantByGroup($appGroup=false)
    {
        if(!$appGroup)
            if(!($appGroup = request()->header('Group-App',false))){
                if(!($appGroup = request()->route('group_app',false))){ 
                    $appGroup = request()->input('group_app',false);
                }
            }
        
        // jika mengakses aplikasi tenant
        if(
            (!$appGroup && config('AppConfig.system.multitenant.owner_subfolder','')=='') || 
            ($appGroup && $appGroup == config('AppConfig.system.multitenant.owner_subfolder'))
        ){
            $this->setTenantManagementIsActive();
        }else{

            $tenant = $this->_getTenantByGroupApp($appGroup);//$this->getTenantModel()->where('group_app',$appGroup)->first();
            if($tenant)
                $this->setActiveTenant($tenant->toArray());
        }
    }
    
    /**
     * set active tenant by domain
     * fungsi ini dieksekusi di RouteServiceProvider utama jika multi tentant nya didetect via subdomain
     */
    public function setActiveTenantByDomain($domain=false)
    {
        $domain = $domain?$domain:request()->getHttpHost();
        
        // jika mengakses domain owner maka tandai sebagai koneksi domain owner
        if($domain==config('AppConfig.system.multitenant.owner_domain')){
            $this->setTenantManagementIsActive();
        }else{
            $tenant = $this->_getTenantByDomain($domain);//$this->getTenantModel()->where('domain',$domain)->first();
            if($tenant)
                $this->setActiveTenant($tenant->toArray());
        }
    }

    public function setActiveTenant(array $dataTenant)
    {
        $config = app('config');
        $config->set('tenant',$dataTenant);
        
        // set packageLocal pertenant
        if(config('AppConfig.packageLocalPerTenant.'.$dataTenant['id']))
            $config->set('AppConfig.packageLocal',config('AppConfig.packageLocalPerTenant.'.$dataTenant['id']));

        resolve('bindTenant',['tenant_id'=>$dataTenant['id']]);

        $this->setDb($dataTenant['id']);
        
    }

    public function getActiveTenant(string $field = '')
    {
        return config($field?('tenant.'.$field):'tenant');
    }
    
    /**
     * set aplikasi yang sedang aktif adalah tenant management (owner) bukan aplikasi per tenantnya
     */
    public function setTenantManagementIsActive()
    {
        $config = app('config');
        $config->set('tenant',['isTenantManagementActive'=>true]);
    }

    /**
     * apakah yang aktif sekarang adalah aplikasi tenant managementnya ?
     * 
     * @return Boolean true jika yang aktif adalah aplikasi tenant management, false jika bukan
     */
    public function isTenantManagementActive() 
    {
        return config('tenant.isTenantManagementActive',false);
    }
    
    
    /**
     * END - GROUP MANAGE ACTIAVE TENANT
     */

    /**
     * CRUD tenant
     */
    public function createTenant($input)
    {
        $return = $this->_autoResourceCreate('createTenant',[$input]);
        // setelah proses create pastikan _tenant.json diupdate
        \App\Services\Utilities::artisan('synapse:updateTenantList');
        return $return;
    }

    public function deleteTenant($where)
    {
        $oldTenant = $this->_autoResourceGet('getTenant',[$where]);
        if($oldTenant){
            $return = $this->_autoResourceDelete('deleteTenant',[$where]);
            $this->_autoResourceDelete('deleteGroup',[['tenant_id',$oldTenant['id']]]);
            // setelah proses delete pastikan _tenant.json diupdate
            \App\Services\Utilities::artisan('synapse:updateTenantList');
            return $return;
        }
        $this->error = __('lang.data_attribute_not_found',['attribute'=>'Tenant']);
        return false;
    }    
    
    /**
     * STORAGE
     */

    /**
     * get disk yang digunakan tenant yang disertakan, jika tidak ada maka akan
     * return disk default.
     * 
     * @param Integer Id tenant, jika 0 berarti akan ambil tenant yang aktif
     * 
     * @return String nama disknya
     */
    public function storageGetDisk($tenantId=0)
    {
        $disk = config('filesystems.default');
        $serverId = config('tenant.s3storage',0);
        
        // jika mendefinisikan id tenant
        if($tenantId && config('tenant.id',0) != $tenantId){
            $tmpTenant = $this->getTenantModel()->select('s3storage')->where('id',$tenantId)->first();
            if($tmpTenant){
                $serverId = $tmpTenant->s3storage;
            }
        }

        // jika multi tenant aktif dan menggunakan s3 storage
        if(
            config('AppConfig.system.multitenant.active') && 
            config('filesystems.s3_multi_server') && 
            $serverId != 0 && 
            config('filesystems.disks.s3_'.$serverId,false)!=false
        ){
            $disk = 's3_'.config('tenant.s3storage');
        }

        return $disk;
    }

    /**
     * get Storage instance per tenant
     * 
     * @param Integer Id tenant, jika 0 berarti akan ambil tenant yang aktif
     * @return StorageInstance
     */
    public function storage($tenantId=0)
    {
        return Storage::disk($this->storageGetDisk($tenantId));
    }

    /**
     * get apakah tenant tersebut menggunakan storage S3 atau tidak
     * 
     * @param Integer Id tenant, jika 0 berarti akan ambil tenant yang aktif
     */
    public function storageIsS3($tenantId=0)
    {
        return config('filesystems.disks.'.$this->storageGetDisk($tenantId).'.driver','local') == 's3'?true:false;
    }

    /**
     * get apakah tenant tersebut menggunakan storage S3 per tenant
     * 
     * @param Integer Id tenant, jika 0 berarti akan ambil tenant yang aktif
     */
    public function storageIsS3tenant($tenantId=0)
    {
        $serverId = config('tenant.s3storage',0);
        if($tenantId && config('tenant.id',0) != $tenantId){
            $tmpTenant = $this->getTenantModel()->select('s3storage')->where('id',$tenantId)->first();
            if($tmpTenant){
                $serverId = $tmpTenant->s3storage;
            }
        }
        return $serverId>0?true:false;
    }
}