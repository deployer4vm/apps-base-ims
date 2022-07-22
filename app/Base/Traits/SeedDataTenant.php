<?php

namespace App\Base\Traits;

use Illuminate\Support\Facades\DB;
use App\Facades\Tenant;

/**
 * use trait ini di seed yang datanya ada pemisahan antar tenantnya 
 */
trait SeedDataTenant
{
    /**
     * Mode seed dijalan dari mana :
     *      true jika seed dijalan dari Tenant service
     *      false jika seed dijalan kan dari fitur artisan db:seed
     */
    public $_tenantSeedMode = false;

    public function setTenantId($tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function setTenantSeedMode($tenantSeedMode)
    {
        $this->_tenantSeedMode = $tenantSeedMode;
    }

    /**
     * Mode seed dijalan dari mana
     * 
     * @return Boolean
     *      true jika seed dijalan dari Tenant service
     *      false jika seed dijalan kan dari fitur artisan db:seed
     */
    public function tenantSeedMode()
    {
        return $this->_tenantSeedMode;
    }

    public function dbTable($table)
    {
        //jika mode nya 1 tenant 1 database
        if(config('AppConfig.system.multitenant.data_mode',1)==3){   
            return DB::connection(config('database.perTenant').$this->tenantId)->table($table); 
        // jika dalam 1 database utama
        }else{
            return DB::table($table);
        }
    }
    
    public function dbTableExists($table)
    {
        return Tenant::tableExists($table,$this->tenantId);
    }

}