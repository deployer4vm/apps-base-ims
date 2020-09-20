<?php

namespace App\Base;

use DateTimeInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

use App\Facades\CacheConfig;

class BaseModel extends Model
{
    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    /**
     * override fillable
     */
    public function getFillable()
    {
        if(empty($this->fillable))$this->setAutoFillable();
        return $this->fillable;
    }

    public function setAutoFillable()
    {
        $fields = Schema::connection($this->getConnectionName())->getColumnListing($this->getTable());
        $guarded = $this->getGuarded();
        $this->fillable = array_filter($fields,function($v) use ($guarded) {
            return !in_array($v,$guarded);
        });

    }
    
    /**
     * BELUM DIGUNAKAN : hasilnya masih belum sesuai, jadi nanti pikirikan lagi
     * fungsinya untuk load list field tidak live query ke database, tapi ambil dari cache
     */
    public function setAutoFillableWithCache()
    {
        $tableName = $this->getTable();
        $guarded = $this->getGuarded();
        $fillable = CacheConfig::getConfig('autoFillable-'.$tableName,false,false);
        if(!$fillable){
            $fields = Schema::connection($this->getConnectionName())->getColumnListing($tableName);
            $fillable = array_filter($fields,function($v) use ($guarded) {
                return !in_array($v,$guarded);
            });
            CacheConfig::setConfig('autoFillable-'.$tableName,$fillable);
        }
        return $fillable;
    }
    
    public function createdby()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\User', 'created_by', 'id');
    }
        
    public function updatedby()
    {
        return $this->belongsTo('hpsynapse\moduser\Models\User', 'updated_by', 'id');
    }
}
