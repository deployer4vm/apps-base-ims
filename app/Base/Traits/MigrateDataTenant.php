<?php

namespace App\Base\Traits;

use Illuminate\Support\Facades\Schema;
use App\Facades\Tenant;

/**
 * use trait ini di model yang datanya ada pemisahan antar tenantnya 
 */
trait MigrateDataTenant
{
    public function createPerTenant($table,$bluePrint)
    {
        //jika mode nya tidak share dalam 1 table
        if(config('AppConfig.system.multitenant.data_mode',1)!=1){        
            $filter = isset($this->tenantId)?[['id',$this->tenantId]]:[];
            $tenantList = Tenant::listTenant($filter);  
            foreach ($tenantList['data'] as $tenant) {     
                //jika per database
                if(config('AppConfig.system.multitenant.data_mode')==3){                    
                    Tenant::setDb($tenant['id']);
                    if (Tenant::dbExists($tenant['id']) && !Schema::connection(config('database.perTenant').$tenant['id'])->hasTable($table)) {
                        Schema::connection(config('database.perTenant').$tenant['id'])->create($table,$bluePrint);
                    }
                // jika per table
                }else{                
                    $table = Tenant::getTableName($table,$tenant['id']);
                    if (!Schema::hasTable($table)) {
                        Schema::create($table,$bluePrint);
                    }
                }
            }    
        // jika di 1 table
        }else{
            Schema::create($table,$bluePrint);
        }
    }

    public function tablePerTenant($table,$bluePrint,$column=false,$ifColumnExist=false)
    { 
        //jika mode nya tidak share dalam 1 table
        if(config('AppConfig.system.multitenant.data_mode',1)!=1){        
            $filter = isset($this->tenantId)?[['id',$this->tenantId]]:[];
            $tenantList = Tenant::listTenant($filter);  
            foreach ($tenantList['data'] as $tenant) {    
                //jika per database
                if(config('AppConfig.system.multitenant.data_mode',1)==3){                    
                    Tenant::setDb($tenant['id']);
                    if (Tenant::dbExists($tenant['id']) && Schema::connection(config('database.perTenant').$tenant['id'])->hasTable($table)) {
                        if(
                            $column==false ||
                            (!$ifColumnExist && !Schema::connection(config('database.perTenant').$tenant['id'])->hasColumn($table,$column)) || 
                            ($ifColumnExist && Schema::connection(config('database.perTenant').$tenant['id'])->hasColumn($table,$column))
                        )
                            Schema::connection(config('database.perTenant').$tenant['id'])->table($table,$bluePrint);
                    }
                // jika per table
                }else{                
                    $table = Tenant::getTableName($table,$tenant['id']);
                    if (Schema::hasTable($table)) {
                        if(
                            $column==false ||
                            (!$ifColumnExist && !Schema::hasColumn($table,$column)) || 
                            ($ifColumnExist && Schema::hasColumn($table,$column))
                        )
                            Schema::table($table,$bluePrint);
                    }
                }
            }    
        // jika di 1 table
        }else{
            Schema::table($table,$bluePrint);
        }
    }

    
    public function dropTablePerTenant($table,$ifTableExist=true)
    {
        //jika mode nya tidak share dalam 1 table
        if(config('AppConfig.system.multitenant.data_mode',1)!=1){        
            $filter = isset($this->tenantId)?[['id',$this->tenantId]]:[];
            $tenantList = Tenant::listTenant($filter);  
            foreach ($tenantList['data'] as $tenant) {     
                //jika per database
                if(config('AppConfig.system.multitenant.data_mode',1)==3){                    
                    Tenant::setDb($tenant['id']);
                    if (Tenant::dbExists($tenant['id'])) {
                        if($ifTableExist){
                            Schema::connection(config('database.perTenant').$tenant['id'])->dropIfExists($table);
                        }else{
                            Schema::connection(config('database.perTenant').$tenant['id'])->drop($table);
                        }  
                    }
                // jika per table
                }else{                
                    $table = Tenant::getTableName($table,$tenant['id']);
                    
                    if($ifTableExist){
                        Schema::dropIfExists($table);
                    }else{
                        Schema::drop($table);
                    }  
                }
            }    
        // jika di 1 table
        }else{
            if($ifTableExist){
                Schema::dropIfExists($table);
            }else{
                Schema::drop($table);
            }            
        }
    }

}