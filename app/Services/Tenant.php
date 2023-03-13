<?php

namespace App\Services;

use Exception;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

use App\Base\BaseRepository;

use App\Models\Tenant as MTenant;
use App\Models\TenantDomain;
use App\Models\TenantGroup;
use App\Models\TenantGroupTenant;

class Tenant extends BaseRepository
{   
    
    protected $autoResource = [
        'Tenant' => ['r'=>MTenant::class,'w'=>MTenant::class],
        'TenantDomain' => ['r'=>TenantDomain::class,'w'=>TenantDomain::class],
        'TenantGroup' => ['r'=>TenantGroup::class,'w'=>TenantGroup::class],
        'TenantGroupTenant' => ['r'=>TenantGroupTenant::class,'w'=>TenantGroupTenant::class],
    ];
    
    protected $autoResourceSearchField = [
        'Tenant' => ['group_app','domain','name','note'],
        'TenantDomain' => ['domain','redirect'],
        'TenantGroup' => ['name'],
    ];    
    
    // public function listTenant(array $filter = [], int $offset = 0, int $limit = 0, array $orderBy = [])
    // {
    //     return $this->_autoResourceList('listTenant',[$filter,$offset,$limit,$orderBy]);
    // }

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
        
    private $_tmpTenantList=[],
    $_tmpTenantListByGroupApp=[],
    $_tmpTenantListByDomain=[];
    
    public function getTenantById($tenantId)
    {
        if(!isset($this->_tmpTenantList[$tenantId])){
            
            if(!($this->_tmpTenantList[$tenantId] = $this->getTenantModel()->where('id',$tenantId)->first())){
                $this->error = 'Tenant not found';
                return false;
            }

            $this->_tmpTenantList[$tenantId] = $this->_tmpTenantList[$tenantId]->toArray();
            
            $this->_tmpTenantListByGroupApp[$this->_tmpTenantList[$tenantId]['group_app']] = $this->_tmpTenantList[$tenantId];
            $this->_tmpTenantListByDomain[$this->_tmpTenantList[$tenantId]['domain']] = $this->_tmpTenantList[$tenantId];
        }

        return $this->_tmpTenantList[$tenantId];
    }

    
    public function getTenantByGroupApp($groupApp)
    {
        if(!isset($this->_tmpTenantListByGroupApp[$groupApp])){
            $this->_tmpTenantListByGroupApp[$groupApp] = $this->getTenantModel()->where('group_app',$groupApp)->first();
            $this->_tmpTenantList[$this->_tmpTenantListByGroupApp[$groupApp]['id']] = $this->_tmpTenantListByGroupApp[$groupApp];
            $this->_tmpTenantListByDomain[$this->_tmpTenantListByGroupApp[$groupApp]['domain']] = $this->_tmpTenantListByGroupApp[$groupApp];
        }

        return $this->_tmpTenantListByGroupApp[$groupApp];
    }

    public function getTenantByDomain($domain=false)
    {
        // sementara, khusus di local, jangan di PUSH
        // $domain = str_replace('smartcoopv2.localhost','smartcoop.localhost',$domain);
        $domain = $domain?$domain:request()->getHttpHost();
        if(!isset($this->_tmpTenantListByDomain[$domain])){

            $domainData = TenantDomain::where('domain',$domain)->where('status','!=',0)->first();
            $this->_tmpTenantListByDomain[$domain] = $domainData?$this->getTenantModel()->where('id',$domainData['tenant_id'])->first():false;

            // $this->_tmpTenantListByDomain[$domain] = $this->getTenantModel()->whereHas('domain',function($m) use($domain){
            //     $m->where('domain',$domain)->where('status',1);
            // })->first();

            if($this->_tmpTenantListByDomain[$domain]){
                $this->_tmpTenantListByDomain[$domain] = $this->_tmpTenantListByDomain[$domain]->toArray();
                $this->_tmpTenantListByDomain[$domain]['domain'] = $domainData->toArray();
            }


            $this->_tmpTenantListByGroupApp[$this->_tmpTenantListByDomain[$domain]['group_app']] = $this->_tmpTenantListByDomain[$domain];
            $this->_tmpTenantList[$this->_tmpTenantListByDomain[$domain]['id']] = $this->_tmpTenantListByDomain[$domain];
        }

        return $this->_tmpTenantListByDomain[$domain];
    }

    /**
     * START - GROUP MANAGE ACTIVE TENANT
     */

    public function setActiveTenantById($tenantId)
    {
        $tenant = $this->getTenantById($tenantId);//$this->getTenantModel()->where('id',$tenantId)->first();
        if($tenant)
            $this->setActiveTenant($tenant);
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
            $this->setOnTenantManager();
        }else{

            $tenant = $this->getTenantByGroupApp($appGroup);//$this->getTenantModel()->where('group_app',$appGroup)->first();
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
        $domain = explode(':',$domain);
        
        // jika mengakses domain owner maka tandai sebagai koneksi domain owner
        if($domain[0]==config('AppConfig.system.multitenant.owner_domain')){
            $this->setOnTenantManager();

        // jika mengakses domain storage all tenant maka tandai sebagai koneksi domain storage
        }else if($domain==config('AppConfig.system.multitenant.alltenant_storage_domain')){
            $this->setOnStorageAlltenant();

        // jika mengakses domain general API maka tandai sebagai koneksi domain api
        }else if($domain==config('AppConfig.system.multitenant.general_api_domain')){
            $this->setOnGeneralApi();                
            
        }else{
            $tenant = $this->getTenantByDomain($domain[0]);//$this->getTenantModel()->where('domain',$domain)->first();
            if($tenant)
                $this->setActiveTenant($tenant);
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
    public function setOnTenantManager()
    {
        $config = app('config');
        $config->set('tenant',['isOnTenantManager'=>true]);
    }
    
    /**
     * set aplikasi yang sedang aktif adalah domain cdn/storage per tenant
     */
    public function setOnStorageAlltenant()
    {
        $config = app('config');
        $config->set('tenant',['isOnStorageAlltenant'=>true]);
    }

    /**
     * set aplikasi yang sedang aktif adalah domain api general
     */
    public function setOnGeneralApi()
    {
        $config = app('config');
        $config->set('tenant',['isOnGeneralApi'=>true]);
    }

    /**
     * apakah yang aktif sekarang adalah aplikasi tenant managementnya ?
     * 
     * @return Boolean true jika yang aktif adalah aplikasi tenant management, false jika bukan
     */
    public function isOnTenantManager() 
    {
        return config('tenant.isOnTenantManager',false);
    }    

    /**
     * apakah yang aktif sekarang adalah storage all tenant
     * 
     * @return Boolean true jika yang aktif adalah storage all tenant, false jika bukan
     */
    public function isOnStorageAlltenant() 
    {
        return config('tenant.isOnStorageAlltenant',false);
    }
    
    /**
     * apakah yang aktif sekarang adalah domain api general ?
     * 
     * @return Boolean true jika yang diakses adalah domain api general, false jika bukan
     */
    public function isOnGeneralApi() 
    {
        return config('tenant.isOnGeneralApi',false);
    }
    
    /**
     * END - GROUP MANAGE ACTIAVE TENANT
     */

    /**
     * CRUD tenant
     */
    public function createTenant($input)
    {
        if(!isset($input['group_app'])){
            return false;
        }
        
        $input['domain'] = $input['group_app'] . '.' . config('AppConfig.system.multitenant.main_domain');

        // pastikan default config sudah terset
        if(!isset($input['config'])){
            $input['config'] = [
                'storage_limit'=>0,
                'db_limit'=>0,
                'resource_limit'=>0,
            ];
        }
        
        $return = $this->_autoResourceCreate('createTenant',[$input]);
        if($return){
            $this->_autoResourceCreate('createTenantDomain',[[
                'tenant_id'=>$return['id'],
                'domain'=>$input['domain'],
                'status'=>1
            ]]);
        }
        // setelah proses create pastikan _tenant.json diupdate
        \App\Services\Utilities::artisan('synapse:updateTenantList');
        \App\Services\Utilities::artisan('config:cache');
        return $return;
    }

    public function updateTenant($where,$data=array())
    {
        $oldTenant = $this->getTenant($where);
        if(!$oldTenant){
            $this->error = __('lang.data_attribute_not_found',['attribute'=>'Tenant']);
            return false;
        }

        if(isset($data['group_app']) && $oldTenant['group_app'] != $data['group_app']){
            $data['domain'] = $data['group_app'] . '.' . config('AppConfig.system.multitenant.main_domain');
            $this->_autoResourceUpdate('updateTenantDomain',[
                [
                    ['domain',$oldTenant['domain']],
                    ['tenant_id',$oldTenant['id']],
                ],
                [                    
                    'domain'=>$data['domain']
                ]
            ]);
        }

        $return = $this->_autoResourceUpdate('updateTenant',[
            $where,
            $data
        ]);

        // jika update status, db, s3storage
        if(isset($data['status']) || isset($data['db']) || isset($data['s3storage'])){
            \App\Services\Utilities::artisan('synapse:updateTenantList');
            \App\Services\Utilities::artisan('config:cache');
        }
        
        return $return;
    }

    public function deleteTenant($where)
    {
        $oldTenant = $this->_autoResourceGet('getTenant',[$where]);
        if($oldTenant){
            $return = $this->_autoResourceDelete('deleteTenant',[$where]);
            $this->_autoResourceDelete('deleteTenantGroupTenant',[['tenant_id',$oldTenant['id']]]);
            $this->_autoResourceDelete('deleteTenantDomain',[['tenant_id',$oldTenant['id']]]);
            // setelah proses delete pastikan _tenant.json diupdate
            \App\Services\Utilities::artisan('synapse:updateTenantList');
            \App\Services\Utilities::artisan('config:cache');
            return $return;
        }
        $this->error = __('lang.data_attribute_not_found',['attribute'=>'Tenant']);
        return false;
    }    
    

    /**
     * START - GROUP MANAGE PEMISAHAN DATABASE ATAU TABLE PER TENANT
     */
    
    private function getTenantModel()
    {
        if(config('AppConfig.system.multitenant.table_instance',false)==false){
            return new MTenant;
        }

        return MTenant::with(['instanceData']);
    }

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
                if(!($tenant = $this->getTenantById($tenantId)))
                    return false;
                $server = config('database.multi_database_server.servers.'.$tenant['db']);
            }else{
                $server = config('database.multi_database_server.servers.'.config('tenant.db'));
            }
            
            return $server;
        }

    public function getDbSize($tenantId=0)
    {
        if($this->dbExists($tenantId)){
            $result = $this->db($tenantId)->select(DB::raw('SELECT table_name AS "Table",
                ((data_length + index_length) / 1024 / 1024) AS "Size"
                FROM information_schema.TABLES
                WHERE table_schema = "'.$this->getDbConnection($tenantId)['database'].'"
                ORDER BY (data_length + index_length) DESC'));
            $size = array_sum(array_column($result, 'Size'));
            return round((float) $size, 2);
        }
        return 0;
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
    
    public function tableColumnExists($tableName,$columnName,$tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $this->getDbConnection($tenantId);// generate dulu confignya
        return Schema::connection($this->getDbConnectionName($tenantId))->hasColumn($tableName,$columnName);
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
     * men-seed seluruh seed per tenant di 1 tenant baru
     */
    public function seed($tenantId)
    {
        //jika database belum ada maka tolak
        if (!$this->dbExists($tenantId)) {
            return false;
        }

        $seeds = config('hpsynapse.seed_path');
        foreach ($seeds as $seedpath) {
            $seedFileList = glob($seedpath.DIRECTORY_SEPARATOR.'*.php');
            foreach ($seedFileList as $seed) {  
                include_once $seed;
                $seedClass = ucfirst(Str::camel(substr(str_replace('.php','',basename($seed)),18)));                
                $tmpClass = new $seedClass;
                // hanya meng-seed yang seed pertenant saja
                if(method_exists($tmpClass,'tenantSeedMode')){
                    if(!property_exists($tmpClass,'tenantId') || $tmpClass->tenantId==$tenantId){
                        $tmpClass->setTenantSeedMode(true);
                        $tmpClass->setTenantId($tenantId);
                        $tmpClass->run();
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
        // jika multi tenant dalam 1 database beda table
        if(config('AppConfig.system.multitenant.data_mode')==2){
            return config('AppConfig.system.multitenant.table_prefix','_').$tenantId.'_'.$tableName;
        }else{            
            return $tableName;
        }
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
     * STORAGE
     */

    /**
     * get disk yang digunakan tenant yang disertakan, jika tidak ada maka akan
     * return disk default.
     * 
     * @param Integer $tenantId         Id tenant, jika 0 berarti akan ambil tenant yang aktif
     * @param Boolean $isPublic         
     * 
     * @return String nama disknya
     */
    public function storageGetDisk($tenantId=0,$isPublic=false)
    {
        // jika mendefinisikan tenant ID
        if(config('AppConfig.system.multitenant.active') && $tenantId){// && config('tenant.id',0) != $tenantId){            
            $serverId = config('AppConfig.tenant.'.$tenantId.'.s3storage',0);            

            // jika multi tenant aktif dan menggunakan s3 storage
            if(            
                config('filesystems.s3_multi_server') && 
                $serverId != 0 && 
                config('filesystems.disks.s3_'.$serverId,false)!=false
            ){
                $disk = 's3_'.$serverId;
            }else{
                // set storage baru
                if($isPublic){
                    $disk = config(
                        'filesystems.disks.public_tenant_'.$tenantId.'.name',
                        $this->setStorageLocalPublicDisk($tenantId)
                    );
                }else{
                    $disk = config(
                        'filesystems.disks.local_tenant_'.$tenantId.'.name',
                        $this->setStorageLocalDisk($tenantId)
                    );
                    
                }
            }
    
            return $disk;
        }

        return config('filesystems.default');
    }

    /**
     * get disk local yang digunakan tenant yang disertakan, jika tidak ada maka akan
     * return disk default.
     * 
     * @param Integer $tenantId         Id tenant, jika 0 berarti akan ambil tenant yang aktif
     * @param Boolean $isPublic         
     * 
     * @return String nama disknya
     */
    public function storageGetDiskLocal($tenantId=0,$isPublic=false)
    {
        // jika mendefinisikan tenant ID
        if(config('AppConfig.system.multitenant.active') && $tenantId){// && config('tenant.id',0) != $tenantId){            
            
            // set storage baru
            if($isPublic){
                $disk = config(
                    'filesystems.disks.public_tenant_'.$tenantId.'.name',
                    $this->setStorageLocalPublicDisk($tenantId)
                );
            }else{
                $disk = config(
                    'filesystems.disks.local_tenant_'.$tenantId.'.name',
                    $this->setStorageLocalDisk($tenantId)
                );                
            }
    
            return $disk;
        }

        return config('filesystems.default');
    }

    /**
     * set config storage local per tenant
     * 
     * @param Integer $tenantId Id tenant
     * 
     * @return String nama disk nya
     */
    public function setStorageLocalDisk(int $tenantId)
    {        
        $tmpConfig = config('filesystems.disks.local');
        $tmpConfig['name'] = 'local_tenant_'.$tenantId;
        $tmpConfig['root_old'] = isset($tmpConfig['root_old'])?$tmpConfig['root_old']:$tmpConfig['root'];
        $tmpConfig['root'] = $tmpConfig['root_old'].'/tenant_'.$tenantId;
        app()->config['filesystems.disks.local_tenant_'.$tenantId] = $tmpConfig;

        return $tmpConfig['name'];
    }

    /**
     * set config storage local public per tenant
     * 
     * @param Integer $tenantId Id tenant
     * 
     * @return String nama disk nya
     */
    public function setStorageLocalPublicDisk(int $tenantId)
    {        
        $tmpConfig = config('filesystems.disks.public');
        if($tmpConfig['driver']=='s3')
            $tmpConfig = config('filesystems.disks.local');

        $tmpConfig['name'] = 'public_tenant_'.$tenantId;
        $tmpConfig['root_old'] = isset($tmpConfig['root_old'])?$tmpConfig['root_old']:$tmpConfig['root'];
        $tmpConfig['root'] = $tmpConfig['root_old'].'/tenant_'.$tenantId;
        $tmpConfig['visibility'] = 'public';
        app()->config['filesystems.disks.public_tenant_'.$tenantId] = $tmpConfig;

        return $tmpConfig['name'];
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
        return config('AppConfig.tenant.'.$tenantId.'.s3storage',0)?true:false;
    }

    /**
     * @return Float Ukuran storage dalam MB
     */
    public function storageSize($tenantId){
        
        $bytes = $this->storageSize_byDisk($this->storageGetDisk($tenantId));

        // jika storage s3 maka kalkulasi juga disk storage local nya
        if($this->storageIsS3($tenantId)){
            $bytes +=  $this->storageSize_byDisk(config(
                'filesystems.disks.local_tenant_'.$tenantId.'.name',
                $this->setStorageLocalDisk($tenantId)
            ));
            $bytes +=  $this->storageSize_byDisk(config(
                'filesystems.disks.public_tenant_'.$tenantId.'.name',
                $this->setStorageLocalDisk($tenantId)
            ));
        }

        return round($bytes/1024/1024,2);
    }

    private function storageSize_byDisk($disk)
    {
        return array_sum(
            array_map(
                function($file) {
                    return (float) $file['size'];
                }, 
                array_filter(
                    Storage::disk($disk)->listContents('/', true /*<- recursive*/), 
                    function($file) {
                        return $file['type'] == 'file';
                    }
                )
            )
        );
    }

    private function storageSize_byDir($dir)
    {
        $total_size = 0;
        $count = 0;
        $dir_array = scandir($dir);
        foreach($dir_array as $key=>$filename){
            if($filename!=".." && $filename!="."){
                if(is_dir($dir."/".$filename)){
                    $new_foldersize = $this->storageSize_byDir($dir."/".$filename);
                    $total_size = $total_size+ $new_foldersize;
                }else if(is_file($dir."/".$filename)){
                    $total_size = $total_size + filesize($dir."/".$filename);
                    $count++;
                }
            }
        }
        return $total_size;
    }
}