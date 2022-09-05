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

    public $tenantId = 0;

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
        //jika mode nya tidak share dalam 1 table
        if(config('AppConfig.system.multitenant.active',false) && config('AppConfig.system.multitenant.data_mode',1)!=1){        
              
            //jika per database
            if(config('AppConfig.system.multitenant.data_mode',1)==3){     
                return DB::connection(config('database.perTenant').$this->tenantId)->table($table); 
            // jika per table pake prefix nama table
            }else{
                $tmpTable = Tenant::getTableName($table,$this->tenantId);
                return DB::table($tmpTable);
            }
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