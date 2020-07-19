<?php

namespace App\Services;

use App\Models\Tenant as MTenant;
use App\Models\TenantGroup;
use App\Models\TenantGroupTenant;

use App\Base\BaseRepository;

class Tenant extends BaseRepository
{   
    
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
    }

    public function getActiveTenant(string $field = '')
    {
        return config($field?('tenant.'.$field):'tenant');
    }
    
    /**
     * END - GROUP MANAGE ACTIAVE TENANT
     */
}