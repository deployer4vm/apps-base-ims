<?php

namespace App\Services;

use App\Models\Tenant as MTenant;
use App\Models\TenantGroup;
use App\Models\TenantGroupTenant;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

use App\Base\BaseRepository;

class Tenant extends BaseRepository
{   
    
    protected $autoResource = [
        'Tenant' => ['r'=>MTenant::class,'w'=>MTenant::class],
        'Group' => ['r'=>TenantGroupTenant::class,'w'=>TenantGroupTenant::class],
    ];
    
    /**
     * START - GROUP MANAGE PEMISAHAN DATABASE ATAU TABLE PER TENANT
     */

    /**
     * generate nama koneksi database per tenant
     */
    public function getDbConnectionName($tenantId)
    {
        return config('database.perTenant').$tenantId;
    }

    /**
     * generate and get connection database pertenant
     */
    public function getDbConnection($tenantId)
    {
        $dbConfigName = $this->getDbConnectionName($tenantId);
        $dbConfig = config('database.connections.'.config('database.perTenant'));
        $dbConfig['database'] = $this->getDbName($tenantId);

        // jika multidatabase server aktif maka detek dan sinkronkan konfig db nya
        if(config('database.multi_database_server.enable',false))
            $dbConfig = $this->getDbConnection_getServer($tenantId,$dbConfig);

        config(['database.connections.'.$dbConfigName => $dbConfig]);

        return $dbConfig;
    }

    private function getDbConnection_getServer($tenantId,$dbConfig)
    {
        if(config('tenant.id')!=$tenantId){
            $tenant = MTenant::select('db')->where('id',$tenantId)->first();
            $server = config('database.multi_database_server.servers.'.$tenant->db);
        }else{
            $server = config('database.multi_database_server.servers.'.config('tenant.db'));
        }
        $dbConfig['host'] = $server['host'];
        return $dbConfig;
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
        $tenant = MTenant::where('id',$tenantId)->first();

        if($tenant->db==0){
            $schemaName = config("database.connections.".config("database.perTenant").".database_prefix").$tenantId;
        }else{
            $schemaName = config("database.multi_database_server.servers.".$tenant->db.".database_prefix").$tenantId;
        }

        return $schemaName;
    }

    /**
     * cek apakah database pertenant sudah ada
     */
    public function dbExists($tenantId)
    {
        $schemaName = $this->getDbName($tenantId);
        $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?";
        $db = DB::select($query, [$schemaName]);
        
        //jika empty berarti database belum ada
        return empty($db)?false:true;
    }

    /**
     * set connection active database per tenant session saat ini
     */
    public function setDb($tenantId)
    {        
        $dbConfigName = $this->getDbConnectionName($tenantId);
        config(['tenant.connection',$dbConfigName]);

        // tambah connection database on thy fly sesuai tenant yang aktifnya (jika belum ditambah)
        if(config('database.connections.'.$dbConfigName,false)==false){
            $dbConfig = $this->getDbConnection($tenantId);
            config(['database.connections.'.$dbConfigName => $dbConfig]);
        }
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
    public function dbBeginTransactionIfNotExist($that, $func, $rollbackFunc=null){
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
        //
        if(config('AppConfig.system.multitenant.table_instance',false)==false){
            return new MTenant;
        }

        return MTenant::with(['instanceData']);
    }
    
    public function setActiveTenantById($tenantId)
    {
        $tenant = $this->getTenantModel()->where('id',$tenantId)->first();
        if($tenant)
            $this->setActiveTenant($tenant->toArray());
    }

    public function setActiveTenantByGroup($appGroup)
    {
        $tenant = $this->getTenantModel()->where('group_app',$appGroup)->first();
        if($tenant)
            $this->setActiveTenant($tenant->toArray());
    }

    public function setActiveTenantByDomain($domain=false)
    {
        $domain = $domain?$domain:request()->getHttpHost();
        $tenant = $this->getTenantModel()->where('domain',$domain)->first();
        if($tenant)
            $this->setActiveTenant($tenant->toArray());
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
}