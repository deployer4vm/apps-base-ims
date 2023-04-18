<?php

namespace App\Base\Traits;

use App\Facades\Tenant;
use Illuminate\Support\Facades\Log;

/**
 * use trait ini di model yang datanya ada pemisahan antar tenantnya 
 */
trait ModelDataTenant
{
    protected $tenantId = 0;
    
    /**
     * Overide data model jika diperlukan
     * 
     * 1 mode share dalam 1 table
     * 2 mode beda table
     * 3 mode beda database
     */
    public function getDataMode()
    {
        return config('AppConfig.system.multitenant.data_mode',1);
    }
    
    /**
     * set tenant aktif model ini
     */
    public function setTenantId($tenantId,$isTenantId=true)
    {
        $this->tenantId = $tenantId;

        // $searchField = $isTenantId?'id':'group_app';
        // if($tenantId!=config('tenant.'.$searchField)){
        //     $tenantData = Tenant::getTenant([$searchField,$this->tenantId]);
        //     $config = app('config');
        //     $config->set('tenant',$tenantData);
        // }

        // $GLOBALS['model_tenant_id'] = app('tenant.id');
        // $this->tenantId = app('tenant.id');
    }

    public function getTenantId()
    {
        if (empty($this->tenantId))
            $this->tenantId = $GLOBALS['model_tenant_id'] = isset($GLOBALS['model_tenant_id'])?$GLOBALS['model_tenant_id']:config('tenant.id');
                   
        return $this->tenantId;
    }

    public function setDbPerTenant()
    {
        if (empty($this->tenantId))
            $this->tenantId = $GLOBALS['model_tenant_id'] = isset($GLOBALS['model_tenant_id'])?$GLOBALS['model_tenant_id']:config('tenant.id');
        
        Tenant::setDb($this->tenantId);
    }
    
    public function getConnectionName()
    {
        //get tenant id yang terset di model ini
        if (empty($this->tenantId))
            $this->tenantId = isset($GLOBALS['model_tenant_id'])?$GLOBALS['model_tenant_id']:config('tenant.id');
            
        if(config('AppConfig.system.multitenant.data_mode',1)==3){
            if($this->connection != Tenant::getDbConnectionName($this->tenantId)){
                $this->setDbPerTenant();
                $this->connection = Tenant::getDbConnectionName($this->tenantId);
            }
        }else{
            $this->connection = config('database.perTenant');
        }
        
        return parent::getConnectionName(); 
    }

    public function setTable($table)
    {
        // jika sebelumnya nama table dengan prefix tenant telah diset,
        // maka tolak set nama table baru
        if(config('AppConfig.system.multitenant.data_mode',1)==2){
            if (empty($this->tenantId))
                $this->tenantId = isset($GLOBALS['model_tenant_id'])?$GLOBALS['model_tenant_id']:config('tenant.id');    
            $prefix = empty($this->tenantId)?'':(config('AppConfig.system.multitenant.table_prefix','_').$this->tenantId.'_');
            $table = $prefix.$this->table;
        }else{
            $table = $this->table;
        }

        $this->table = $table;

        return $this;
    }

    public function getTable()
    {
        if (empty($this->tenantId))
            $this->tenantId = isset($GLOBALS['model_tenant_id'])?$GLOBALS['model_tenant_id']:config('tenant.id');

        $table = $this->table;
        if(config('AppConfig.system.multitenant.data_mode',1)==2){// && !$this->_tableNameSetted){
            $prefix = empty($this->tenantId)?'':(config('AppConfig.system.multitenant.table_prefix','_').$this->tenantId.'_');
            $table = $prefix.$this->table;
        }
        
        return $table;
    }
}
