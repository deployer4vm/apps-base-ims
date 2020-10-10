<?php

namespace App\Services;

use App\Models\Tenant as MTenant;
use App\Models\TenantGroup;
use App\Models\TenantGroupTenant;

use Illuminate\Support\Facades\DB;

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
        config(['database.connections.'.$dbConfigName => $dbConfig]);

        return $dbConfig;
    }

    /**
     * generate and get nama database untuk database pertenant
     */
    public function getDbName($tenantId)
    {
        return config('database.connections.'.config('database.perTenant').'.database_prefix').$tenantId;
    }

    /**
     * cek apakah database pertenant sudah ada
     */
    public function dbExists($tenantId)
    {
        $schemaName = config("database.connections.".config("database.perTenant").".database_prefix").$tenantId;
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
     * begin db transaction pertenant, hanya eksekusi di multi tenant db yg sudah di-initialize sebelumnya
     */
    public function dbBeginTransaction($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        \Illuminate\Support\Facades\DB::connection($this->getDbConnectionName($tenantId))->beginTransaction();
    }

    /**
     * commit db transaction pertenant, hanya eksekusi di multi tenant db yg sudah di-initialize sebelumnya
     */
    public function dbCommit($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        \Illuminate\Support\Facades\DB::connection($this->getDbConnectionName($tenantId))->commit();
    }

    /**
     * rollback db transaction pertenant, hanya eksekusi di multi tenant db yg sudah di-initialize sebelumnya
     */
    public function dbRollback($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        $tenantId = $tenantId?$tenantId:$this->getActiveTenant('id');
        \Illuminate\Support\Facades\DB::connection($this->getDbConnectionName($tenantId))->rollback();
    }

    /**
     * db per tenant
     */
    public function db($tenantId=false)
    {
        if(!$tenantId)$tenantId=config('tenant.id');
        return \Illuminate\Support\Facades\DB::connection($this->getDbConnectionName($tenantId));
    }

    /**
     * END - GROUP MANAGE PEMISAHAN DATABASE ATAU TABLE PER TENANT
     */
        
    /**
     * START - GROUP MANAGE ACTIAVE TENANT
     */

    public function setActiveTenantById($tenantId)
    {
        $tenant = MTenant::where('id',$tenantId)->first();
        if($tenant)
            $this->setActiveTenant($tenant->toArray());
    }

    public function setActiveTenantByGroup($appGroup)
    {
        $tenant = MTenant::where('group_app',$appGroup)->first();
        if($tenant)
            $this->setActiveTenant($tenant->toArray());
    }

    public function setActiveTenant(array $dataTenant)
    {
        $config = app('config');
        $config->set('tenant',$dataTenant);
        resolve('bindTenant',['tenant_id'=>$dataTenant['id']]);
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