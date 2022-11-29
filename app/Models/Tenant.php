<?php

namespace App\Models;

use App\Base\BaseModel;

class Tenant extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenants';
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id','created_at'];

    protected $casts  = [
        'config' => 'array',
    ];

    /**
     * relasi data ke instansi terkait
     */
    public function instanceData()
    {
        return $this->hasOne(config('AppConfig.system.multitenant.table_instance'),'tenant_id', 'id');
    }
    
    public function domain()
    {
        return $this->hasMany(TenantDomain::class,'tenant_id','id');
    }
}