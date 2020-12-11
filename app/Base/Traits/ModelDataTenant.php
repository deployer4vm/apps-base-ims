<?php

namespace App\Base\Traits;

use App\Facades\Tenant;

/**
 * use trait ini di model yang datanya ada pemisahan antar tenantnya 
 */
trait ModelDataTenant
{
    protected $tenantId = 0;
    
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

        if(
            config('AppConfig.system.multitenant.data_mode',1)==3 &&
            $this->connection != Tenant::getDbConnectionName($this->tenantId)
        ){
            $this->setDbPerTenant();
            $this->connection = Tenant::getDbConnectionName($this->tenantId);
        }
        
        return parent::getConnectionName(); 
    }
    
    public function getTable()
    {
        if (empty($this->tenantId))
            $this->tenantId = isset($GLOBALS['model_tenant_id'])?$GLOBALS['model_tenant_id']:config('tenant.id');

        $table = $this->table;
        if(config('AppConfig.system.multitenant.data_mode',1)==2)
            $table = config('AppConfig.system.multitenant.table_prefix','_').$this->tenantId.'_'.$this->table;
        
        return $table;
    }
}
